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

use Backdrop\Container\Container;
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

	public function testBindsAndResolvesValues(): void {

		$this->container->add( 'name', 'Backdrop' );

		$this->assertTrue( $this->container->has( 'name' ) );
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

		$this->assertTrue( $this->container->has( 'nothing' ) );
		$this->assertNull( $this->container->resolve( 'nothing' ) );
	}

	public function testReturnsFalseForMissingBindings(): void {

		$this->assertFalse( $this->container->has( 'missing' ) );
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

	public function testAliases(): void {

		$this->container->singleton( Dependency::class );
		$this->container->alias( Dependency::class, 'dependency' );

		$this->assertTrue( $this->container->has( 'dependency' ) );
		$this->assertSame( $this->container->resolve( Dependency::class ), $this->container->resolve( 'dependency' ) );

		// Removing by alias removes the original binding.
		$this->container->remove( 'dependency' );

		$this->assertFalse( $this->container->has( 'dependency' ) );
		$this->assertFalse( $this->container->has( Dependency::class ) );
	}

	public function testRebindingClearsTheSharedInstance(): void {

		$this->container->singleton( 'value', function() {
			return 'first';
		} );

		$this->container->resolve( 'value' );

		$this->container->singleton( 'value', function() {
			return 'second';
		} );

		$this->assertSame( 'second', $this->container->resolve( 'value' ) );
	}

	public function testExtensionsDecorateResolvedObjects(): void {

		$this->container->singleton( 'object', function() {
			return new stdClass();
		} );

		$this->container->extend( 'object', function( $object ) {
			$object->extended = true;
			return $object;
		} );

		$this->assertTrue( $this->container->resolve( 'object' )->extended );
	}

	public function testExtensionsAddedBeforeTheBindingAreKept(): void {

		$this->container->extend( 'object', function( $object ) {
			$object->extended = true;
			return $object;
		} );

		$this->container->bind( 'object', function() {
			return new stdClass();
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

	public function testExplicitParametersOverrideAutoWiring(): void {

		$dependency = new Dependency();

		$object = $this->container->resolve( NeedsDependency::class, [
			'dependency' => $dependency,
			'label'      => 'custom',
		] );

		$this->assertSame( $dependency, $object->dependency );
		$this->assertSame( 'custom', $object->label );
	}

	public function testUnresolvableDependenciesThrow(): void {

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Unable to resolve dependency [string] for parameter [$name]' );

		$this->container->resolve( NeedsString::class );
	}

	public function testAbstractClassesAreNotBuilt(): void {

		$this->assertFalse( $this->container->resolve( AbstractThing::class ) );
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

class NeedsString {

	public function __construct( string $name ) {}
}

abstract class AbstractThing {}
