<?php

/**
 * Класс для переопределения файлов шаблона
 */
class Smarty_Resource_File extends Smarty_Internal_Resource_File
{
    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source $source source object
     * @param Smarty_Internal_Template $_template template object
     * @throws SmartyException if source cannot be loaded
     * @return void
     */
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
    {
        $source->name = Engine::getInstance()->Plugin_GetDelegate('template', $source->name);
        parent::populate($source, $_template);
    }
}

// EOF