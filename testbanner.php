<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class TestBanner extends Module
{
    public function __construct()
    {
        $this->name = 'testbanner';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Wutzy Wutz';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Test banner', [], 'Modules.Testbanner.Admin');
        $this->description = $this->trans('Displaying an original banner test.', [], 'Modules.Testbanner.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Testbanner.Admin');

        if (!Configuration::get('TESTBANNER_NAME')) {
            $this->warning = $this->trans('No name provided', [], 'Modules.Testbanner.Admin');
        }
    }

    public function install()
    {
        return parent::install() &&
        $this->registerHook('displayBanner') &&
        $this->registerHook('actionFrontControllerSetMedia') &&
        // if we want disable the module after installation we use : $this->disable()
        Configuration::updateValue('TESTBANNER_NAME', 'Test banner') &&
        Configuration::updateValue('TESTBANNER_COLOR', '#cccccc') &&
        Configuration::updateValue('TESTBANNER_STATE', 0);
    }

    public function uninstall()
    {
        return (
            parent::uninstall()
            && Configuration::deleteByName('TESTBANNER_NAME')
            && Configuration::deleteByName('TESTBANNER_COLOR')
            && Configuration::deleteByName('TESTBANNER_STATE')
        );
    }

    /**
     * This method handles the module's configuration page
     * @return string The page's HTML content
     */
    public function getContent()
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {

            // retrieve the value set by the user
            $configColorValue = (string) Tools::getValue('TESTBANNER_COLOR');

            // check that the value is valid
            if (empty($configColorValue) || !Validate::isGenericName($configColorValue)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue('TESTBANNER_COLOR', $configColorValue);
                Configuration::updateValue('TESTBANNER_STATE', (int) Tools::getValue('TESTBANNER_STATE'));
                $output = $this->displayConfirmation('Settings updated successfully');
            }
        }

        // display any message, then the form
        return $output . $this->displayForm();
    }

    /**
     * Builds the configuration form
     * @return string HTML code
     */
    public function displayForm()
    {
        // Init Fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' =>
                [
                    [
                        'type' => 'text',
                        'label' => $this->l('Color'),
                        'name' => 'TESTBANNER_COLOR',
                        'size' => 20,
                        'required' => true,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enable banner', [], 'Modules.Testbanner.Admin'),
                        'name' => 'TESTBANNER_STATE',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Yes', [], 'Admin.Global'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('No', [], 'Admin.Global'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value['TESTBANNER_COLOR'] = Tools::getValue('TESTBANNER_COLOR', Configuration::get('TESTBANNER_COLOR'));
        $helper->fields_value['TESTBANNER_STATE'] = Tools::getValue('TESTBANNER_STATE', Configuration::get('TESTBANNER_STATE'));
        return $helper->generateForm([$form]);
    }

    public function hookDisplayBanner($params)
    {

        $this->context->smarty->assign([
            'test_banner_color' => Configuration::get('TESTBANNER_COLOR'),
            'test_banner_state' => Configuration::get('TESTBANNER_STATE'),
            //check if the user is logged
            'is_user' => Context::getContext()->customer->isLogged(),
            //find the name of current controller to check the current page
            'current_page' => Tools::getValue('controller')
        ]);

        return $this->display(__FILE__, 'testbanner.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'testbanner-style',
            'modules/' . $this->name . '/views/css/testbanner.css'
        );

        $this->context->controller->registerJavascript(
            'testbanner-javascript',
            'modules/' . $this->name . '/views/js/testbanner.js',
            [
                'position' => 'bottom',
                'priority' => 1000,
            ]
        );
    }

}
