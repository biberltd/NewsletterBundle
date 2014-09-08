<?php
/**
 * @name        NewsletterLocalization
 * @package		BiberLtd\Core\NewsletterBundle
 *
 * @author		Murat Ünal
 * @version     1.0.0
 * @date        10.09.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Bundle\NewsletterBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="newsletter_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_u_newsletter_localization", columns={"newsletter","language"})}
 * )
 */
class NewsletterLocalization extends CoreEntity
{
    /** 
     * @ORM\Column(type="string", length=45, nullable=false)
     */
    private $subject;

    /** 
     * @ORM\Column(type="text", nullable=false)
     */
    private $content;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\NewsletterBundle\Entity\Newsletter",
     *     inversedBy="localizations"
     * )
     * @ORM\JoinColumn(name="newsletter", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $newsletter;

    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $language;

    /**
     * @name                  setContent ()
     *                                   Sets the content property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $content
     *
     * @return          object                $this
     */
    public function setContent($content) {
        if(!$this->setModified('content', $content)->isModified()) {
            return $this;
        }
		$this->content = $content;
		return $this;
    }

    /**
     * @name            getContent ()
     *                             Returns the value of content property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->content
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @name                  setLanguage ()
     *                                    Sets the language property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $language
     *
     * @return          object                $this
     */
    public function setLanguage($language) {
        if(!$this->setModified('language', $language)->isModified()) {
            return $this;
        }
		$this->language = $language;
		return $this;
    }

    /**
     * @name            getLanguage ()
     *                              Returns the value of language property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->language
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * @name                  setNewsletter ()
     *                                      Sets the newsletter property.
     *                                      Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $newsletter
     *
     * @return          object                $this
     */
    public function setNewsletter($newsletter) {
        if(!$this->setModified('newsletter', $newsletter)->isModified()) {
            return $this;
        }
		$this->newsletter = $newsletter;
		return $this;
    }

    /**
     * @name            getNewsletter ()
     *                                Returns the value of newsletter property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->newsletter
     */
    public function getNewsletter() {
        return $this->newsletter;
    }

    /**
     * @name                  setSubject ()
     *                                   Sets the subject property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $subject
     *
     * @return          object                $this
     */
    public function setSubject($subject) {
        if(!$this->setModified('subject', $subject)->isModified()) {
            return $this;
        }
		$this->subject = $subject;
		return $this;
    }

    /**
     * @name            getSubject ()
     *                             Returns the value of subject property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->subject
     */
    public function getSubject() {
        return $this->subject;
    }
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 10.09.2013
 * **************************************
 * A getContent()
 * A getLanguage()
 * A getNewsletter()
 * A getSubject()
 *
 * A setContent()
 * A setLanguage()
 * A setNewsletter()
 * A setSubject()
 *
 */