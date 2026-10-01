<?php
/**
 * Container tests.
 *
 * @package   Backdrop
 * @author    Benjamin Lu <benlumia007@gmail.com>
 * @copyright 2019 Benjamin Lu
 * @license   https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/backdrop-dev/framework
 */

namespace Backdrop\Tests\Unit;

use ArrayObject;
use Backdrop\Core\Container;
use Backdrop\Tests\TestCase;
use RuntimeException;
use stdClass;

class ContainerTest extends TestCase {

	/**
	 * @var Container
	 */
	protected $container;

	protected function setUp(): void {

		parent::setUp();

		$this->container = new Container();
	}

	public function testAddsAndResolvesValues(): void {

		$this->container->add( 'name', 'Backdrop' );

		$this->assertTrue( $this->container->bound( 'name' ) );
		$this->assertSame( 'Backdrop', $this->container->resolve( 'name' ) );
		$this->assertSame( 'Backdrop', $this->container->get( 'name' ) );
	}

	/**
	 * @dataProvider falsyValues
	 *
	 * @param mixed $value Falsy value.
	 */
	public function testStoresFalsyValues( $value ): void {

		$this->container->add( 'value', $value );

		$this->assertTrue( $this->container->bound( 'value' ) );
		$this->assertSame( $value, $this->container->resolve( 'value' ) );
	}

	public function falsyValues(): array {

		return [
			'zero'         => [ 0 ],
			'string zero'  => [ '0' ],
			'empty string' => [ '' ],
			'false'        => [ false ],
			'empty array'  => [ [] ],
		];
	}

	public function testStoresNullInstances(): void {

		$this->container->instance( 'nothing', null );

		$this->assertTrue( $this->container->bound( 'nothing' ) );
		$this->assertNull( $this->container->resolve( 'nothing' ) );
	}

	public function testReturnsFalseForMissingBindings(): void {

		$this->assertFalse( $this->container->bound( 'missing' ) );
		$this->assertFalse( $this->container->resolve( 'missing' ) );
	}

	public function testClosuresReceiveTheContainerAndParameters(): void {

		$this->container->bind( 'greeting', function( $container, $params ) {
			return [ $container, $params['name'] ];
		} );

		list( $container, $name ) = $this->container->resolve( 'greeting', [ 'name' => 'Ben' ] );

		$this->assertSame( $this->container, $container );
		$this->assertSame( 'Ben', $name );
	}

	public function testBindingsAreNotSharedByDefault(): void {

		$this->container->bind( 'object', function() {
			return new stdClass();
		} );

		$this->assertNotSame( $this->container->resolve( 'object' ), $this->container->resolve( 'object' ) );
	}

	public function testSingletonsAreShared(): void {

		$this->container->singleton( 'object', function() {
			return new stdClass();
		} );

		$this->assertSame( $this->container->resolve( 'object' ), $this->container->resolve( 'object' ) );
	}

	public function testBindReplacesASharedInstance(): void {

		$this->container->bind( 'value', function() {
			return 'first';
		}, true );

		$this->container->resolve( 'value' );

		$this->container->bind( 'value', function() {
			return 'second';
		}, true );

		$this->assertSame( 'second', $this->container->resolve( 'value' ) );
	}

	public function testAddReplacesAnExistingBinding(): void {

		$this->container->add( 'name', 'first' );
		$this->container->add( 'name', 'second' );

		$this->assertSame( 'second', $this->container->resolve( 'name' ) );
	}

	public function testSingletonReplacesAnExistingBindingAndItsInstance(): void {

		$this->container->singleton( 'service', function() {
			return 'first';
		} );

		$this->assertSame( 'first', $this->container->resolve( 'service' ) );

		$this->container->singleton( 'service', function() {
			return 'second';
		} );

		$this->assertSame( 'second', $this->container->resolve( 'service' ) );
	}

	public function testArrayAndPropertyAssignmentReplaceExistingValues(): void {

		$this->container['key'] = 'first';
		$this->container['key'] = 'second';

		$this->container->name = 'first';
		$this->container->name = 'second';

		$this->assertSame( 'second', $this->container['key'] );
		$this->assertSame( 'second', $this->container->name );
	}

	public function testInstanceIsReplacedByANewBinding(): void {

		$this->container->instance( 'value', 'instance' );
		$this->container->add( 'value', 'binding' );

		$this->assertSame( 'binding', $this->container->resolve( 'value' ) );
	}

	public function testExtensionsAddedBeforeTheBindingAreKept(): void {

		$this->container->extend( 'object', function( $object ) {
			$object->extended = true;
			return $object;
		} );

		$this->container->singleton( 'object', function() {
			return new stdClass();
		} );

		$this->assertTrue( $this->container->resolve( 'object' )->extended );
	}

	public function testExtensionsSurviveReplacingTheBinding(): void {

		$this->container->bind( 'object', function() {
			return new stdClass();
		} );

		$this->container->extend( 'object', function( $object ) {
			$object->extended = true;
			return $object;
		} );

		$this->container->bind( 'object', function() {
			$object        = new stdClass();
			$object->fresh = true;
			return $object;
		} );

		$object = $this->container->resolve( 'object' );

		$this->assertTrue( $object->fresh );
		$this->assertTrue( $object->extended );
	}

	public function testAliases(): void {

		$this->container->singleton( Dependency::class );
		$this->container->alias( Dependency::class, 'dependency' );

		$this->assertSame(
			$this->container->resolve( Dependency::class ),
			$this->container->resolve( 'dependency' )
		);
	}

	public function testRemove(): void {

		$this->container->add( 'name', 'Backdrop' );
		$this->container->remove( 'name' );

		$this->assertFalse( $this->container->bound( 'name' ) );
	}

	public function testRemoveByAlias(): void {

		$this->container->add( 'real', 'value' );
		$this->container->alias( 'real', 'nickname' );
		$this->container->extend( 'real', function( $value ) {
			return $value;
		} );

		$this->container->remove( 'nickname' );

		$this->assertFalse( $this->container->bound( 'real' ) );
		$this->assertFalse( $this->container->resolve( 'nickname' ) );
	}

	public function testExtensionsDecorateResolvedObjects(): void {

		$this->container->bind( 'object', function() {
			return new stdClass();
		} );

		$this->container->extend( 'object', function( $object, $container ) {
			$object->container = $container;
			return $object;
		} );

		$this->assertSame( $this->container, $this->container->resolve( 'object' )->container );
	}

	public function testSingletonsStoreTheExtendedObject(): void {

		$this->container->singleton( 'object', function() {
			return new stdClass();
		} );

		$this->container->extend( 'object', function( $object ) {
			return new ArrayObject( [ 'inner' => $object ] );
		} );

		$first = $this->container->resolve( 'object' );

		$this->assertInstanceOf( ArrayObject::class, $first );
		$this->assertSame( $first, $this->container->resolve( 'object' ) );
	}

	public function testExtendViaAlias(): void {

		$this->container->bind( 'object', function() {
			return new stdClass();
		} );

		$this->container->alias( 'object', 'thing' );

		$this->container->extend( 'thing', function( $object ) {
			$object->extended = true;
			return $object;
		} );

		$this->assertTrue( $this->container->resolve( 'object' )->extended );
	}

	public function testAutoWiresClassDependencies(): void {

		$this->container->singleton( Dependency::class );

		$object = $this->container->resolve( NeedsDependency::class );

		$this->assertInstanceOf( NeedsDependency::class, $object );
		$this->assertSame( $this->container->resolve( Dependency::class ), $object->dependency );
		$this->assertSame( 'default', $object->label );
		$this->assertNull( $object->optional );
	}

	public function testAutoWiresBoundInterfaces(): void {

		$this->container->singleton( Service::class, ServiceImplementation::class );

		$object = $this->container->resolve( NeedsService::class );

		$this->assertInstanceOf( ServiceImplementation::class, $object->service );
		$this->assertSame( $this->container->resolve( Service::class ), $object->service );
	}

	public function testExplicitParametersOverrideAutoWiring(): void {

		$dependency = new Dependency();

		$object = $this->container->resolve( NeedsDependency::class, [
			'dependency' => $dependency,
			'label'      => null,
		] );

		$this->assertSame( $dependency, $object->dependency );
		$this->assertNull( $object->label );
	}

	public function testUnresolvableDependenciesThrowAClearError(): void {

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Unable to resolve dependency [Backdrop\Tests\Unit\Unbound] for parameter [$unbound]' );

		$this->container->resolve( NeedsUnbound::class );
	}

	public function testUnresolvableBuiltInTypesThrowAClearError(): void {

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Unable to resolve dependency [string] for parameter [$name]' );

		$this->container->resolve( NeedsString::class );
	}

	public function testAbstractClassesAreNotBuilt(): void {

		$this->assertFalse( $this->container->resolve( AbstractThing::class ) );
	}

	public function testUnionTypesReceiveOneArgument(): void {

		if ( PHP_VERSION_ID < 80000 ) {
			$this->markTestSkipped( 'Union types require PHP 8.0.' );
		}

		eval( 'namespace Backdrop\Tests\Unit; class NeedsUnion { public $args; public function __construct( Dependency|\stdClass $value, $label = "label" ) { $this->args = func_get_args(); } }' );

		$object = $this->container->resolve( NeedsUnion::class );

		$this->assertCount( 2, $object->args );
		$this->assertInstanceOf( Dependency::class, $object->args[0] );
		$this->assertSame( 'label', $object->args[1] );
	}

	public function testArrayAccess(): void {

		$this->container['zero'] = 0;

		$this->assertTrue( isset( $this->container['zero'] ) );
		$this->assertSame( 0, $this->container['zero'] );

		unset( $this->container['zero'] );

		$this->assertFalse( isset( $this->container['zero'] ) );
	}

	public function testMagicProperties(): void {

		$this->container->name = 'Backdrop';

		$this->assertTrue( isset( $this->container->name ) );
		$this->assertSame( 'Backdrop', $this->container->name );

		unset( $this->container->name );

		$this->assertFalse( isset( $this->container->name ) );
	}

	public function testConstructorDefinitions(): void {

		$container = new Container( [ 'a' => 1, 'b' => 0 ] );

		$this->assertSame( 1, $container->resolve( 'a' ) );
		$this->assertSame( 0, $container->resolve( 'b' ) );
	}
}

class Dependency {}

interface Unbound {}

interface Service {}

class ServiceImplementation implements Service {}

class NeedsDependency {

	public $dependency;

	public $label;

	public $optional;

	public function __construct( Dependency $dependency, $label = 'default', ?Unbound $optional = null ) {

		$this->dependency = $dependency;
		$this->label      = $label;
		$this->optional   = $optional;
	}
}

class NeedsService {

	public $service;

	public function __construct( Service $service ) {

		$this->service = $service;
	}
}

class NeedsUnbound {

	public function __construct( Unbound $unbound ) {}
}

class NeedsString {

	public function __construct( string $name ) {}
}

abstract class AbstractThing {}
