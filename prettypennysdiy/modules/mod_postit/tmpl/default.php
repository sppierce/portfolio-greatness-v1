<?php 

/**

 * Post It Module

 *

 * A module to display notes in a post-it-note style.

 * 

 * @author Polished Geek

 * @package mod_postit

 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

 *

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,

 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR

 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM,

 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING

 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE

 * SOFTWARE.

 */



// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script src="//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js"></script>

<script>



  WebFont.load({



    google: {



      families: ['<?php echo $params->get('font-family');







?>']



    }



  });



</script>

<?php
    header("Content-type: text/css; charset: UTF-8");

   $showorhide = "#990000";
   
?>


<style type="text/css">

@font-face {

	font-family: 'allthatmattersmedium';

	src: url('/modules/mod_postit/assets/fonts/allthatmatters-webfont.eot');

	src: url('/modules/mod_postit/assets/fonts/allthatmatters-webfont.eot?#iefix') format('embedded-opentype'),  url('/modules/mod_postit/assets/fonts/allthatmatters-webfont.woff') format('woff'),  url('/modules/mod_postit/assets/fonts/allthatmatters-webfont.ttf') format('truetype'),  url('/modules/mod_postit/assets/fonts/allthatmatters-webfont.svg#allthatmattersmedium') format('svg');

	font-weight: normal;

	font-style: normal;

}

.<?php echo $params->get('class');?>.post-it-note {

 max-height:<?php echo $params->get('max-height').'%';

 ?>;



 max-width:<?php echo $params->get('max-width').'%';

 ?>;

 font-size:<?php echo $params->get('font-size').'px';

 ?>;

 color:<?php echo $params->get('color');

?>!important;

 font-family:"<?php echo $params->get('font-family');

 ?>";

 line-height:<?php echo $params->get('line-height').'px';

 ?> !important;

 background-color:<?php echo $params->get('note-color');

 ?>;

 padding-top:<?php echo $params->get('padding-top').'px';

 ?>;

 padding-right:<?php echo $params->get('padding-right').'px';

 ?>;

 padding-bottom:<?php echo $params->get('padding-bottom').'px';

 ?>;

 padding-left:<?php echo $params->get('padding-left').'px';

 ?>;

 -webkit-transform: rotate(<?php echo $params->get('degrees').'deg';

 ?>);







	/* Firefox */







	-moz-transform: rotate(<?php echo $params->get('degrees').'deg';

 ?>);







	/* IE */







	-ms-transform: rotate(<?php echo $params->get('degrees').'deg';

 ?>);







	/* Opera */







	-o-transform: rotate(<?php echo $params->get('degrees').'deg';

 ?>);

 box-shadow:<?php echo $params->get('shadow-horizontal').'px';

?> <?php echo $params->get('shadow-vertical').'px';

?> <?php echo $params->get('shadow-blur').'px';

?> <?php echo $params->get('shadow-color');

?>;

}



.<?php echo $params->get('class');?>.editor-output {line-height:<?php echo $params->get('line-height').'px';

 ?> !important;}

.<?php echo $params->get('class');?> ol li, .<?php echo $params->get('class');?> ul li {line-height:<?php echo $params->get('line-height').'px';

 ?> !important;}

.<?php echo $params->get('class');?> .tack {

	position: absolute;

 top:<?php echo $params->get('tack-top').'px';

?>;

 left:<?php echo $params->get('tack-left').'px';

?>;


	background-image: url("<?php echo $params->get('tackpath');?><?php echo $params->get('tack-color');?>");

	background-repeat: no-repeat;

	background-position: center center;

	height: 42px;

	width: 31px;

}
.hidepin.tack {
	display:none !important;
}


.<?php echo $params->get('class');?> .line {

	display: block;

}

.<?php echo $params->get('class');?> .wf-loading h1 {

	visibility: hidden;

}

.<?php echo $params->get('class');?> .wf-active h1, .<?php echo $params->get('class');?> .wf-inactive h1 {

	visibility: visible;

	font-family: 'Cantarell';

}



</style>



<div class="<?php echo $params->get('class');?> post-it-note <?php echo $params->get('customclass');?>">

  <div class="<?php echo $params->get('class');?> <?php echo $params->get('pinvisibleyes');?>  tack"></div>

  <div class="<?php echo $params->get('class');?> text-box-cont"><span class="<?php echo $params->get('class');?> editor-output"><?php echo $params->get('editor');?></span></div>

</div>

