<?php
/**
 * @name        NewsletterCategory
 * @package		BiberLtd\Bundle\CoreBundle\NewsletterBundle
 *
 * @author		Murat Ünal
 * @version     1.0.1
 * @date        10.10.2013
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
 *     name="newsletter_category",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_n_newsletter_category_date_created", columns={"date_created"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_newsletter_category_id", columns={"id"})}
 * )
 */
class NewsletterCategory extends CoreLocalizableEntity
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
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_members;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_messages;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $is_internal;

    /**
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\NewsletterBundle\Entity\Newsletter",
     *     mappedBy="newsletter_category"
     * )
     */
    private $newsletters;

    /**
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\NewsletterBundle\Entity\NewsletterCategoryLocalization",
     *     mappedBy="newsletter_category"
     * )
     */
    protected $localizations;

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
     * @name                  setCountMembers ()
     *                                        Sets the count_members property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_members
     *
     * @return          object                $this
     */
    public function setCountMembers($count_members) {
        if(!$this->setModified('count_members', $count_members)->isModified()) {
            return $this;
        }
		$this->count_members = $count_members;
		return $this;
    }

    /**
     * @name            getCountMembers ()
     *                                  Returns the value of count_members property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_members
     */
    public function getCountMembers() {
        return $this->count_members;
    }

    /**
     * @name                  setCountMessages ()
     *                                         Sets the count_messages property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_messages
     *
     * @return          object                $this
     */
    public function setCountMessages($count_messages) {
        if(!$this->setModified('count_messages', $count_messages)->isModified()) {
            return $this;
        }
		$this->count_messages = $count_messages;
		return $this;
    }

    /**
     * @name            getCountMessages ()
     *                                   Returns the value of count_messages property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_messages
     */
    public function getCountMessages() {
        return $this->count_messages;
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
     * @name            setIsInternal()
     *                  Sets the is_internal property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           string $is_internal
     *
     * @return          object                $this
     */
    public function setIsInternal($is_internal) {
        if(!$this->setModified('is_internal', $is_internal)->isModified()) {
            return $this;
        }
		$this->is_internal = $is_internal;
		return $this;
    }

    /**
     * @name            getIsInternal()
     *                  Returns the value of is_internal property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          string           $this->is_internal
     */
    public function getIsInternal() {
        return $this->is_internal;
    }

    /**
     * @name                  setNewsletters ()
     *                                       Sets the newsletters property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $newsletters
     *
     * @return          object                $this
     */
    public function setNewsletters($newsletters) {
        if(!$this->setModified('newsletters', $newsletters)->isModified()) {
            return $this;
        }
		$this->newsletters = $newsletters;
		return $this;
    }

    /**
     * @name            getNewsletters ()
     *                                 Returns the value of newsletters property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->newsletters
     */
    public function getNewsletters() {
        return $this->newsletters;
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

}
/**
 * Change Log:
 * **************************************
 * v1.0.1                      Murat Ünal
 * 10.10.2013
 * **************************************
 * A getCountMembers()
 * A getCountMessages()
 * A getDateCreated()
 * A getId()
 * A getIsInternal()
 * A getLocalizations()
 * A getNewsletter()
 * A getSite()
 *
 * A setCountMembers()
 * A setCountMessages()
 * A setDateCreated()
 * A setIsInternal()
 * A setLocalizations()
 * A setNewsletter()
 * A setSite()
 *
 */