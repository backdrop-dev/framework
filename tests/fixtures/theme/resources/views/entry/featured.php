<?php // phpcs:ignoreFile ?>
featured:<?php echo esc_html( $title ); ?>|<?php echo $data instanceof Backdrop\Tools\Collection ? "data" : "none"; ?>|<?php echo $view instanceof Backdrop\View\View ? "view" : "none"; ?>
