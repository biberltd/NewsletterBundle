<?php
/**
 * RenderController
 *
 * Used to render standard newsletter views..
 *
 * @vendor      BiberLtd
 * @package		NewsletterBundle
 * @subpackage	Controller
 * @name	    RenderController
 *
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.1
 * @date        02.01.2014
 *
 */

namespace BiberLtd\Core\Bundles\MemberManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class RenderController
{
    private $templating;
    private $theme;

    public  function __construct(EngineInterface $templating){
        $this->templating = $templating;
    }
    /**
     * @name 			renderNewsletterEmail()
     *  				Renders newsletter email.
     *
     * @since			1.0.0
     * @version         1.0.0
     * @author          Can Berkol
     *
     * @see             BiberLtd\Core\Bundles\NewsletterBundle\Resources\views\cms\Pages\email.html.smarty
     *
     * @param           string          $subject
     * @param           string          $content
     * @param           array           $core           Holds core controller information.
     * @param           string          $header
     * @param           array           $phrase
     * @param           array           $link
     * @param           array           $css
     * @param           arraay          $settings
     * @param           array           $contact
     * @param           array           $copyright
     * @param           array           $owner
     * @param           array           $separator
     * @param           array           $social
     *
     * @return          string
     */
    public function renderNewsletterEmail($subject, $content, $core, $header = '', $phrase = array(), $link = array(), $css = array(), $settings = array(), $contact = array(), $copyright = array(), $owner = array(), $separator = array(), $social = array()){
        $translator = new Translator($core['locale']);

        $this->url = $core['url'];
        /**
         * Prepare defaults
         */
        $cssDefaults = array(
            'article'       => array(
                'h1'            => array(
                    'color'         => '#444444',
                    'display'       => 'block',
                    'font'          => array(
                        'family'        => 'Arial',
                        'size'          => '34px',
                        'weight'        => 'bold',
                    ),
                    'line'      => array(
                        'height'        => '100%'
                    ),
                    'margin'        => array(
                        'bottom'        => '10px',
                    ),
                    'text'          => array(
                        'align'         => 'left',
                    ),
                ),
                'h2'            => array(
                    'color'         => '#444444',
                    'display'       => 'block',
                    'font'          => array(
                        'family'        => 'Arial',
                        'size'          => '30px',
                        'weight'        => 'bold',
                    ),
                    'line-height'   => '100%',
                    'margin'        => array(
                        'bottom'        => '10px',
                    ),
                    'text'          => array(
                        'align'         => 'left',
                    ),
                ),
                'h3'            => array(
                    'color'         => '#444444',
                    'display'       => 'block',
                    'font'          => array(
                        'family'        => 'Arial',
                        'size'          => '26px',
                        'weight'        => 'bold',
                    ),
                    'line'          => array(
                        'height'        => '100%',
                    ),
                    'margin'        => array(
                        'bottom'        => '10px',
                    ),
                    'text'          => array(
                        'align'         => 'left',
                    ),
                ),
                'h4'            => array(
                    'color'         => '#444444',
                    'display'       => 'block',
                    'font'          => array(
                        'family'        => 'Arial',
                        'size'          => '22px',
                        'weight'        => 'bold',
                    ),
                    'line-height'   => '100%',
                    'margin'        => array(
                        'bottom'        => '10px',
                    ),
                    'text'          => array(
                        'align'         => 'left',
                    ),
                ),
            ),
            'bgtable'       => array(
                'height'        => '100%',
                'margin'        => array(
                    'all'           => '0',
                ),
                'padding'       => array(
                    'all'           => '0',
                ),
                'width'         => '100%',
            ),
            'body'          => array(
                'bg'            => array(
                    'color'         => '#FFFFFF',
                ),
            ),
            'bodycontent'   => array(
                'a'             => array(
                    'color'         => '#a02828',
                    'font'          => array(
                        'weight'        => 'normal',
                    ),
                    'text'      => array(
                        'decoration'    => 'underline',
                    ),
                )  ,
                'color'         => '##FFFFFF',
                'font'          => array(
                    'family'        => 'Arial',
                    'size'          => '4PX',
                ),
                'img'           => array(
                    'display'       => 'inline',
                    'margin'        => array(
                        'bottom'        => '10px',
                    ),
                ),
                'line'          => array(
                    'height'        => '150%',
                ),
                'text'          => array(
                    'align'         => 'left',
                ),
            ),
            'container'     => array(
                'bg'            => array(
                    'color'         => '#FFFFFF',
                ),
                'border'        => array(
                    'all'           => 'none',
                ),
            ),
            'footercontent' => array(
                'a'             => array(
                    'color'         => '#a02828',
                    'font'          => array(
                        'weight'        => 'normal',
                    ),
                    'text'      => array(
                        'decoration'    => 'underline' ,
                    ),
                ),
                'color'         => '#707070',
                'font'          => array(
                    'family'        => 'Arial',
                    'size'          => '12px',
                ),
                'img'           => array(
                    'display'       => 'inline',
                ),
                'line'          => array(
                    'height'        => '125%',
                ),
                'text'          => array(
                    'align'         => 'left',
                )
            ),
            'headercontent' => array(
                'a'             => array(
                    'color'         => '#336699',
                    'font'          => array(
                        'weight'        => 'normal',
                    ),
                    'text'          => array(
                       'decoration'     => 'udnerline',
                    ),
                ),
                'color'         => '#444444',
                'h2'            => array(
                    'text'          => array(
                        'align'         => 'center',
                    ),
                ),
                'font'          => array(
                    'family'        => 'Arial',
                    'size'          => '28px',
                    'weight'        => 'bold',
                ),
                'line'          => array(
                    'height'        => '100%',
                ),
                'padding'       => array(
                    'all'           => '4px',
                ),
                'text'          => array(
                    'align'         => 'center',
                ),
                'vertical'      => array(
                    'align'         => 'middle',
                )
            ),
            'headerimage'   => array(
                'height'        => 'auto',
                'max'           => array(
                    'width'         => '600px',
                )
            ),
            'img'           => array(
                'font'          => array(
                    'size'          => '14px',
                    'weight'        => 'bold',
                ),
                'line'          => array(
                    'height'        => '100%',
                ),
                'outline'       => 'none',
                'text'          => array(
                    'decoration'    => 'none',
                    'transform'     => 'capitalize',
                ),
            ),
            'preheader'     => array(
                'bg'            => array(
                    'color'         => '#eeeeee',
                ),
                'border'        => array(
                    'bottom'        => '5px solid #a02828',
                ),
                'div'           => array(
                    'a'             => array(
                        'color'         => '#a02828',
                        'font'          => array(
                            'family'        => 'Arial',
                            'size'          => '10px',
                            'weight'        => 'normal',
                        ),
                        'text'          => array(
                            'decoration'    => 'underline',
                        ),
                    ),
                    'color'     => '#666666',
                    'font'      => array(
                        'family'    => 'Arial',
                        'size'      => '10px',
                    ),
                    'img'       => array(
                        'height'    => 'auto',
                        'max'       => array(
                            'width'     => '600px',
                        ),
                    ),
                    'line'      => array(
                        'height'    => '100%',
                    ),
                    'text'      => array(
                        'align'     => 'left'
                    ),
                ),
            ),
            'social'        => array(
                'a'             => array(
                    'border'        => array(
                        'all'           => '0 none;',
                    ),
                    'display'   => 'block',
                    'float'     => 'left',
                    'margin'    => array(
                        'right'     => '5px',
                    ),
                ),
                'bg'        => array(
                    'color'     => '#FFFFFF',
                ),
                'div'       => array(
                    'text'      => array(
                        'align'     => 'center',
                    ),
                ),
            ),
            'templateheader'=> array(
                'bg'            => array(
                    'color'         => '#FFFFFF',
                ),
                'border'        => array(
                    'bottom'        => '0',
                ),
            ),
            'templatefooter'=> array(
                'bg'            => array(
                    'color'         => '#FFFFFF',
                ),
                'border'        => array(
                    'top'           => '1px solid #dedede;',
                ),
            ),
            'utility'       => array(
                'bg'            => array(
                    'color'         => '#FDFDFD',
                ),
                'border'        => array(
                    'top'           => '1px solid #F5F5F5',
                ),
                'div'       => array(
                    'text'      => array(
                        'align'     => 'center',
                    ),
                ),
            ),
        );
        $contactDefaults = array(
//            array(
//                'type'  => '',
//                'value' => '',
//            ),
        );
        $linkDefaults = array(
            'browser'       => $this->url['base_l'].'/newsletter/'.$core['newsletter_code'],
            'theme'         => $this->url['themes'].'/'.$core['theme'],
            'unsubscribe'   => $this->url['base_l'].'/newsletter/unsubscribe/'.$core['newsletter_code'],
            'update'        => $this->url['base_l'].'/newsletter/update/'.$core['newsletter_code'],
        );
        $ownerDefaults = array(
            'logo'      => $this->url['themes'].'/'.$core['theme'].'css/img/logo_trans.png',
            'name'      => 'Biber Ltd.',
        );
        $phraseDefaults = array(
            'browser'  => $translator->trans('phrase.browser', array(), 'newsletter'),
            'unsubscribe'  => $translator->trans('phrase.unsubscribe', array(), 'newsletter'),
            'update'  => $translator->trans('phrase.update', array(), 'newsletter'),
            'view_on'  => $translator->trans('phrase.view_on', array(), 'newsletter'),
            'view_on_browser'  => $translator->trans('phrase.view_on_browser', array(), 'newsletter'),
        );
        $separatorDefaults = array(
            'img'      => $this->url['themes'].'/'.$core['theme'].'css/img/separator.png',
            'height'   => '408',
            'width'    => '12',

        );
        $settingsDefault = array(
            'copyright'     => true,
            'info'          => true,
            'link'          => true,
            'social'        => true,
            'unsubscribe'   => true,
        );
        $socialDefaults = array();

        /**
         * Merge values and set template variables.
         */
        $vars = array(
            'email' => array(
                'css'       => array_merge($cssDefaults, $css),
                'contact'   => array_merge($contactDefaults, $contact),
                'content'   => $content,
                'copyright' => $copyright,
                'header'    => $header,
                'link'      => array_merge($linkDefaults, $link),
                'owner'     => array_merge($ownerDefaults, $owner),
                'phrase'    => array_merge($phraseDefaults, $phrase),
                'seperator' => array_merge($separatorDefaults, $separator),
                'settings'  => array_merge($settingsDefault, $settings),
                'social'    => array_merge($socialDefaults, $social),
                'subject'   => $subject,
            ),
        );

        return $this->templating->render('BiberLtdCoreBundlesNewsletterBundle:'.$core['theme'].'/Pages:email.html.smarty', $vars);
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Can Berkol
 * 02.01.2014
 * **************************************
 * A renderNewsletterEmail()
 *
 */