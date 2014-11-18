<?php
/**
 * Created by PhpStorm.
 * User: darius
 * Date: 14.11.12
 * Time: 17.50
 */

namespace NfqAkademija\FrontendBundle\EventListener;

use Oneup\UploaderBundle\Event\PostPersistEvent;

class UploadListener
{
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function onUpload(PostPersistEvent $event)
    {
//        $event->doctrine
    }
}
