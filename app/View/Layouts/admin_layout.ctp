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

<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <?php echo $this->Html->charset(); ?>
    <title>
      <?php __('Administration HA Lehmann'); ?>
      <?php echo $title_for_layout; ?>
    </title>
    <?php
    echo $this->Html->meta('icon');
    
    echo $this->Html->css('themes/jquery-ui/ui-darkness/jquery-ui-1.8.16.custom');
    echo $this->Html->css('bootstrap/css/bootstrap');
//    echo $this->Html->css("bootstrap.min");
    echo $this->Html->css('blueimp/jquery.fileupload-ui');
    echo $this->Html->css('blueimp/blueimp-gallery');
    echo $this->Html->css('blueimp/blueimp-gallery-indicator');
    echo $this->Html->css('html5sortable/jquery.sortable');
    echo $this->Html->css("common");
    echo $this->Html->css("muli");
    echo $this->Html->css("lehmann");
    echo $this->Html->css("icons");
    echo $this->Html->css("component");
    echo $this->Html->css("swiper/myswipe");
    echo $this->Html->css("swiper/swiper.min");
    echo $this->Html->css('/js/app/public/application');

    echo $this->Html->scriptStart();
    ?>
    var base_url = '<?php echo $this->Html->url('/'); ?>';
    <?php
    echo $this->Html->scriptEnd();
    echo $this->Html->script('app/public/application');
    ?>

    <?php
    echo $this->Html->scriptStart();
    ?>
    var exports = this;
    $(function() {
      
      var isProduction = true
      
      var categories = <?php echo $this->Js->object($categories); ?>;
      var products = <?php echo $this->Js->object($products); ?>;
      var photos = <?php echo $this->Js->object($photos); ?>;
      var descriptions = <?php echo $this->Js->object($descriptions); ?>;
      
      require("lib/setup")
      Model = Spine.Model
      
      Spine.isProduction = (localStorage.isProduction != null) ? !(localStorage.isProduction === 'false') : isProduction
      
      Category = require('models/category')
      Product = require('models/product')
      Photo = require('models/photo')
      Description = require('models/description')
      
      Settings  = require("models/settings");
      User      = require("models/user");
      Main      = require("admin");
      
      User.ping();
      
      Spine.Route = require('spine/lib/route');
      
      exports.App = new Main({el: $("body")});
      
      
      Description.refresh(descriptions, {clear: true});
      Photo.refresh(photos, {clear: true});
      Product.refresh(products, {clear: true});
      Category.refresh(categories, {clear: true});
      
      Spine.Route.setup()
      
    });
    <?php
    
    echo $this->Html->scriptEnd();
    echo $this->fetch('meta');
    echo $scripts_for_layout;
    ?>
  </head>
  <body class="admin hal cat views canvas" data-model-name="" data-models-name="Root">
    <?php echo $content_for_layout; ?>
    <?php echo $this->element('sql_dump'); ?>
    <?php echo $this->element(STATCONFIG . 'tracking_code'); ?>
  </body>
</html>