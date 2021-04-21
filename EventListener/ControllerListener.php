<?php

namespace A2lix\TranslationFormBundle\EventListener;

use Doctrine\Common\Annotations\Reader,
    Gedmo\Translatable\TranslatableListener,
    Symfony\Component\HttpKernel\Event\FilterControllerEvent,
    Doctrine\Common\Util\ClassUtils;

use Symfony\Component\HttpKernel\Controller\ErrorController;
use Nelmio\ApiDocBundle\Controller\SwaggerUiController;

class ControllerListener
{
    protected $annotationReader;
    protected $translatableListener;

    public function __construct(Reader $annotationReader, TranslatableListener $translatableListener)
    {
        $this->annotationReader = $annotationReader;
        $this->translatableListener = $translatableListener;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        
        $controller = $event->getController();
        if (is_object($controller) && (get_class($controller) == ErrorController::class || get_class($controller) == SwaggerUiController::class)){
            return false;
        }
        
        list($object, $method) = $controller;

        $className = ClassUtils::getClass($object);
        $reflectionClass = new \ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($method);

        if ($this->annotationReader->getMethodAnnotation($reflectionMethod, 'A2lix\TranslationFormBundle\Annotation\GedmoTranslation')) {
            $this->translatableListener->setTranslatableLocale($this->translatableListener->getDefaultLocale());
        }
    }
}