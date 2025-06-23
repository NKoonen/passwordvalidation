<?php

if ( ! defined( '_PS_VERSION_' ) ) {
    exit;
}

class PasswordValidation extends Module
{
    public function __construct()
    {
        $this->name          = 'passwordvalidation';
        $this->tab           = 'checkout';
        $this->version       = '1.0.0';
        $this->author        = 'Inform-All';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        parent::__construct();

        $this->displayName = $this->l( 'Password Validation' );
        $this->description = $this->l(
            'To avoid customer from mistyping their password, this module requires customers to type their password twice.'
        );

        $this->confirmUninstall = $this->l( '' );

        $this->ps_versions_compliancy = [ 'min' => '1.7', 'max' => _PS_VERSION_ ];
    }

    public function install()
    {
        if ( parent::install() == false
             or ! $this->registerHook( 'additionalCustomerFormFields' )
             or ! $this->registerHook( 'validateCustomerFormFields' )
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Add fields in Customer Form
     *
     * @param  array  $params  parameters (@see CustomerFormatter->getFormat())
     *
     * @return array of extra FormField
     */
    public function hookAdditionalCustomerFormFields( $params )
    {
        if ( Tools::getValue( 'controller' ) === "authentication" ) {

            $password_conf_field['password_conf'] = ( new FormField )->setName( 'password_conf' )->setLabel(
                    $this->l( 'Password Confirmation' )
                )->setType( 'password' )->setValue( Tools::getValue( 'password_conf' ) )->setRequired( true );

            return $password_conf_field;
        }
    }

    /**
     * Validate fields in Customer form
     *
     * @param  array  $params  hook call parameters (@see CustomerForm->validateByModules())
     *
     * @return array of extra FormField
     */
    public function hookValidateCustomerFormFields( $params )
    {
        $module_fields = $params['fields'];

        $password_conf = Tools::getValue( 'password_conf' );
        $password      = Tools::getValue( 'password' );

        $module_field_keys = [];
        foreach ( $module_fields as $key => $field ) {
            /** @var FormField $field */
            $module_field_keys[ $field->getName() ] = $key;
        }

        if ( $password_conf != $password ) {
            $module_fields[ $module_field_keys['password_conf'] ]->addError(
                $this->l(
                    'Passwords do not match. Please try again.'
                )
            );
        }

        return [
            $module_fields,
        ];
    }

    public function getContent()
    {
        $this->context->smarty->assign( 'module_dir', $this->_path );

        return $this->context->smarty->fetch( $this->local_path . 'views/templates/admin/configure.tpl' );
    }

}
