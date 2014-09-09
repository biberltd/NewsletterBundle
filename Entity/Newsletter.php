<?php
/**
 * @name        Newsletter
 * @package		BiberLtd\Bundle\NewsletterBundle
 *
 * @author      Can Berkol
 * @author		Murat Ünal
 * @version     1.0.1
 * @date        23.01.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Bundle\NewsletterBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;

/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="newsletter",
 *     options={"engine":"innodb","charset":"utf8","collate":"utf8_turkish_ci"},
 *     indexes={
 *         @ORM\Index(name="idx_n_newsletter_date_created", columns={"date_created"}),
 *         @ORM\Index(name="idx_n_newsletter_date_saved", columns={"date_saved"}),
 *         @ORM\Index(name="idx_n_newsletter_date_sent", columns={"date_sent"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_newsletter_id", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idx_u_newsletter_code", columns={"code"})
 *     }
 * )
 */
class Newsletter extends CoreLocalizableEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_created;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_saved;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_sent;

    /** 
     * @ORM\Column(type="string", unique=true, length=45, nullable=false)
     */
    private $code;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\NewsletterBundle\Entity\NewsletterLocalization",
     *     mappedBy="newsletter"
     * )
     */
    protected $localizations;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\NewsletterBundle\Entity\NewsletterRecipient",
     *     mappedBy="newsletter"
     * )
     */
    private $receipients;

    /** 
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\NewsletterBundle\Entity\NewsletterCategory",
     *     inversedBy="newsletters"
     * )
     * @ORM\JoinColumn(name="category", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $newsletter_category;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

    /**
     * @name            getId()
     *                  Gets $id property.
     * .
     * @author          Murat Ünal
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          integer          $this->id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @name                  setCode ()
     *                                Sets the code property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $code
     *
     * @return          object                $this
     */
    public function setCode($code) {
        if(!$this->setModified('code', $code)->isModified()) {
            return $this;
        }
		$this->code = $code;
		return $this;
    }

    /**
     * @name            getCode ()
     *                          Returns the value of code property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->code
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @name                  setDateCreated ()
     *                                       Sets the date_created property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_created
     *
     * @return          object                $this
     */
    public function setDateCreated($date_created) {
        if(!$this->setModified('date_created', $date_created)->isModified()) {
            return $this;
        }
		$this->date_created = $date_created;
		return $this;
    }

    /**
     * @name            getDateCreated ()
     *                                 Returns the value of date_created property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_created
     */
    public function getDateCreated() {
        return $this->date_created;
    }

    /**
     * @name                  setDateSaved ()
     *                                     Sets the date_saved property.
     *                                     Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_saved
     *
     * @return          object                $this
     */
    public function setDateSaved($date_saved) {
        if(!$this->setModified('date_saved', $date_saved)->isModified()) {
            return $this;
        }
		$this->date_saved = $date_saved;
		return $this;
    }

    /**
     * @name            getDateSaved ()
     *                               Returns the value of date_saved property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_saved
     */
    public function getDateSaved() {
        return $this->date_saved;
    }

    /**
     * @name                  setDateSent ()
     *                                    Sets the date_sent property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_sent
     *
     * @return          object                $this
     */
    public function setDateSent($date_sent) {
        if(!$this->setModified('date_sent', $date_sent)->isModified()) {
            return $this;
        }
		$this->date_sent = $date_sent;
		return $this;
    }

    /**
     * @name            getDateSent ()
     *                              Returns the value of date_sent property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_sent
     */
    public function getDateSent() {
        return $this->date_sent;
    }

    /**
     * @name                  setNewsletterCategory ()
     *                                              Sets the newsletter_category property.
     *                                              Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $newsletter_category
     *
     * @return          object                $this
     */
    public function setNewsletterCategory($newsletter_category) {
        if(!$this->setModified('newsletter_category', $newsletter_category)->isModified()) {
            return $this;
        }
		$this->newsletter_category = $newsletter_category;
		return $this;
    }

    /**
     * @name            getNewsletterCategory ()
     *                                        Returns the value of newsletter_category property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->newsletter_category
     */
    public function getNewsletterCategory() {
        return $this->newsletter_category;
    }

    /**
     * @name                  setSite ()
     *                                Sets the site property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $site
     *
     * @return          object                $this
     */
    public function setSite($site) {
        if(!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
		$this->site = $site;
		return $this;
    }

    /**
     * @name            getSite ()
     *                          Returns the value of site property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->site
     */
    public function getSite() {
        return $this->site;
    }

    /**
     * @name                  setReceipients ()
     *                                       Sets the receipients property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $receipients
     *
     * @return          object                $this
     */
    public function setReceipients($receipients) {
        if($this->setModified('receipients', $receipients)->isModified()) {
            $this->receipients = $receipients;
        }

        return $this;
    }

    /**
     * @name            getReceipients ()
     *                  Returns the value of receipients property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     * @return          mixed           $this->receipients
     */
    public function getReceipients() {
        return $this->receipients;
    }

}
/**
 * Change Log:
 * **************************************
 * v1.0.1                      Can Berkol
 * 23.01.2014
 * **************************************
 * A getReceipients()
 * A setReceipients()
 *
 * **************************************
 * v1.0.0                      Murat Ünal
 * 10.09.2013
 * **************************************
 * A getCode()
 * A getDateCreated()
 * A getDateSaved()
 * A getDateSent()
 * A getId()
 * A getLocalizations()
 * A getNewsletterCategory()
 * A getSite()
 *
 * A setCode()
 * A setDateCreated()
 * A setDateSaved()
 * A setDateSent()
 * A setLocalizations()
 * A setNewsletterCategory()
 * A setSite()
 *
 */