<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.console.libs.templates.skel.views.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <?php echo $this->Html->charset(); ?>
    <title>
      <?php __('CakePHP: Welcome to CakePHP: '); ?>
      <?php echo $title_for_layout; ?>
    </title>
    <?php
    echo $this->Html->meta('icon');
    
    echo $this->Html->css('bootstrap/css/bootstrap');
    echo $this->Html->css('application_boxmodel');
    echo $this->Html->script('app/public/application');

    echo $this->Html->scriptStart();
    ?>

    var base_url = '<?php echo DIR_HOST; ?>';
    var isProduction = true
    var exports = this;
    
    Spine = require('spine');
    Spine.isProduction = (localStorage.isProduction != null) ? !(localStorage.isProduction === 'false') : isProduction
    
    $(function(){
      var Login = require("login");
      exports.Login = new Login({el: $("body")})
    });
    
    <?php
    echo $this->Html->scriptEnd();
    echo $this->fetch('meta');
    echo $scripts_for_layout;
    ?>
  </head>
  <body class="body">
    <?php echo $this->fetch('content'); ?>
    <?php echo $this->element('sql_dump'); ?>
  </body>
</html>