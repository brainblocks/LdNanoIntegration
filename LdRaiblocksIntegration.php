<?php
namespace LdRaiblocksIntegration;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;

class LdRaiblocksIntegration extends Plugin
{
    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        $this->addPayment($context->getPlugin());
        $this->addTokenAttribute();
    }

    private function addTokenAttribute()
    {
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->update('s_order_attributes', 'brainblocks_token', 'text', [
            'displayInBackend' => true,
            'label' => 'Brainblocks Token'
        ]);
    }

    private function addPayment(\Shopware\Models\Plugin\Plugin $plugin)
    {
        /** @var \Shopware\Components\Plugin\PaymentInstaller $installer */
        $installer = $this->container->get('shopware.plugin_payment_installer');

        $options = [
            'name' => 'raiblocks_payment',
            'description' => 'Pay with Raiblocks',
            'action' => 'RaiblocksPayment',
            'active' => 0,
            'position' => 0,
            'additionalDescription' =>
                '<img width="100" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxNi4wLjQsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkZpbmFsIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgd2lkdGg9IjY0OC4wMDVweCIgaGVpZ2h0PSIxNzQuMzE5cHgiIHZpZXdCb3g9IjAgMCA2NDguMDA1IDE3NC4zMTkiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDY0OC4wMDUgMTc0LjMxOSINCgkgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZmlsbD0iIzEyMzIzOSIgZD0iTTE4My42NCw5Ni4zNDZ2MzQuMzQ3aC03Ljc3di04Ny4xMWgzMS45NjNjOS4xMDgsMCwxNS44OTcsMi4wNjgsMjAuMzcxLDYuMjA0DQoJCQljNC40NjcsNC4xMzYsNi43MDMsMTAuNjc0LDYuNzAzLDE5LjYxNWMwLDEzLjk1OC01LjU5NywyMi40MzgtMTYuNzk0LDI1LjQ0N2wxNy45MjMsMzUuODQ1aC04LjY0OGwtMTcuMTczLTM0LjM0N0gxODMuNjR6DQoJCQkgTTIyNi44ODYsNjkuNTI2YzAtNi41Mi0xLjU0Ny0xMS4zMjItNC42MzgtMTQuNDEzYy0zLjA5LTMuMDktNy44OTYtNC42MzctMTQuNDE1LTQuNjM3SDE4My42NHYzOC45NzhoMjQuMTk0DQoJCQlDMjIwLjUzNCw4OS40NTUsMjI2Ljg4Niw4Mi44MTUsMjI2Ljg4Niw2OS41MjZ6Ii8+DQoJCTxwYXRoIGZpbGw9IiMxMjMyMzkiIGQ9Ik0yOTAuNjA2LDg2LjY5NHYzMy41OTZjMC4zMzQsMy4wOTMsNC4yNTUsNC40NTIsOC4xNDYsNC4zODVsLTAuMzc2LDYuMDE4DQoJCQljLTUuOTcxLDAuNjAyLTExLjE1Mi0wLjU0NC0xNC4xNjQtMy44ODhjLTcuODU1LDMuNDMtMTUuNzEzLDUuMTQtMjMuNTYyLDUuMTRjLTUuNjAyLDAtOS44NjMtMS42MDctMTIuNzg3LTQuODI1DQoJCQljLTIuOTI1LTMuMjE5LTQuMzg5LTcuODE0LTQuMzg5LTEzLjc4NmMwLTUuOTc0LDEuNDg1LTEwLjQwMiw0LjQ1My0xMy4yODhjMi45NjctMi44ODEsNy42MjMtNC42NTMsMTMuOTc4LTUuMzI1bDIxLjMwNy0yLjEzNA0KCQkJdi01Ljg5MmMwLTQuNjc0LTEuMDI1LTguMDM2LTMuMDc1LTEwLjA4NWMtMi4wNDQtMi4wNDctNC43ODItMy4wNzItOC4yMDktMy4wNzJjLTUuMzQ5LDAtMTIuMjgyLDAuNTAyLTIwLjgwNywxLjUwM2wtMy44ODMsMC4zNzkNCgkJCWwtMC4zODEtNi4wMTljOS4xOTctMS43NTcsMTcuMjM4LTIuNjMzLDI0LjEyOS0yLjYzM2M2Ljg5NiwwLDExLjg4NSwxLjYyOSwxNC45ODEsNC44ODcNCgkJCUMyODkuMDU5LDc0LjkxNywyOTAuNjA2LDc5LjkyOSwyOTAuNjA2LDg2LjY5NHogTTI1MS4yNDgsMTEyLjg5N2MwLDguNDM1LDMuNDY2LDEyLjY1NiwxMC40MDIsMTIuNjU2DQoJCQljMy4wMTIsMCw2LjMxMS0wLjM1NSw5LjkwMy0xLjA2NmMzLjU5Mi0wLjcwOSw2LjQzNC0xLjQ0LDguNTIzLTIuMTkxbDMuMTM3LTEuMTI5Vjk4LjQ4MmwtMjAuNDM0LDIuMTI4DQoJCQljLTQuMDk0LDAuMzM1LTcuMDQsMS41MDktOC44MzYsMy41MTFDMjUyLjE0NywxMDYuMTI1LDI1MS4yNDgsMTA5LjA0OCwyNTEuMjQ4LDExMi44OTd6Ii8+DQoJCTxwYXRoIGZpbGw9IiMxMjMyMzkiIGQ9Ik0zMDUuNDQ4LDUyLjM1OHYtOC43NzZoNy4zOTN2OC43NzZIMzA1LjQ0OHogTTMwNS40NDgsMTMwLjY5M1Y2OC4wMjFoNy4zOTN2NjIuNjcySDMwNS40NDh6Ii8+DQoJCTxwYXRoIGZpbGw9IiMxMjMyMzkiIGQ9Ik0zMjUuNTI5LDQzLjU4M2gzMi41OWM4Ljc3MywwLDE1LjMzNSwxLjgwMSwxOS42OCw1LjQwNGM0LjM0NSwzLjYwNSw2LjUxNSw5LjM0Niw2LjUxNSwxNy4yMw0KCQkJYzAsNS4xNzEtMC44MTUsOS4yNDQtMi40NDEsMTIuMjExYy0xLjYzMSwyLjk2Ny00LjE5OCw1LjQyNC03LjcwNyw3LjM3N2M4LjE4OCwzLjIxOCwxMi4yODIsOS45NiwxMi4yODIsMjAuMjE1DQoJCQljMCwxNi40NDctOS4xMDgsMjQuNjcyLTI3LjMyNCwyNC42NzJoLTMzLjU5NFY0My41ODN6IE0zNTcuMzY2LDU1Ljc5MWgtMTcuOTI0djI0LjkyN2gxOC40MjZjOC4xODgsMCwxMi4yODctNC4yODUsMTIuMjg3LTEyLjg1DQoJCQlDMzcwLjE1NSw1OS44MTcsMzY1Ljg4OSw1NS43OTEsMzU3LjM2Niw1NS43OTF6IE0zNTguMTE5LDkyLjY3aC0xOC42Nzd2MjUuODE0aDE4LjkyOGM0LjU5MSwwLDguMDQxLTAuOTc2LDEwLjM0Mi0yLjkyNw0KCQkJYzIuMjk2LTEuOTQ1LDMuNDQ1LTUuMjk0LDMuNDQ1LTEwLjA0OWMwLTQuNzQ1LTEuMzM4LTguMDctNC4wMS05Ljk3N0MzNjUuNDcxLDkzLjYyMywzNjIuMTMsOTIuNjcsMzU4LjExOSw5Mi42N3oiLz4NCgkJPHBhdGggZmlsbD0iIzEyMzIzOSIgZD0iTTM5Ni44MTUsMTMwLjY5M3YtODcuMTFoMTMuNjYydjg3LjExSDM5Ni44MTV6Ii8+DQoJCTxwYXRoIGZpbGw9IiMxMjMyMzkiIGQ9Ik00MjcuMzIxLDc0Ljc4OWM0LjMwNC01LjQzLDExLjQ3Mi04LjE0NiwyMS41LTguMTQ2YzEwLjAyMiwwLDE3LjE4NiwyLjcxNiwyMS40OTQsOC4xNDYNCgkJCWM0LjMwNCw1LjQzNSw2LjQ1OCwxMy41ODMsNi40NTgsMjQuNDQzYzAsMTAuODY1LTIuMDkyLDE5LjA0OS02LjI3LDI0LjU2OWMtNC4xNzgsNS41MTQtMTEuNDA5LDguMjctMjEuNjgzLDguMjcNCgkJCWMtMTAuMjg0LDAtMTcuNTExLTIuNzU2LTIxLjY4OC04LjI3Yy00LjE3Ny01LjUyMS02LjI2OS0xMy43MDQtNi4yNjktMjQuNTY5QzQyMC44NjQsODguMzczLDQyMy4wMTksODAuMjI0LDQyNy4zMjEsNzQuNzg5eg0KCQkJIE00MzcuNTM4LDExNS41MjRjMS45MTksMy4zNDIsNS42ODQsNS4wMTYsMTEuMjgzLDUuMDE2YzUuNTk0LDAsOS4zNTQtMS42NzQsMTEuMjc3LTUuMDE2YzEuOTE5LTMuMzM2LDIuODgxLTguODEsMi44ODEtMTYuNDE3DQoJCQljMC03LjYtMS4wMi0xMi45OTMtMy4wNjktMTYuMTcyYy0yLjA0NC0zLjE3MS01Ljc0Ni00Ljc2MS0xMS4wODktNC43NjFjLTUuMzQ5LDAtOS4wNTEsMS41OS0xMS4xMDEsNC43NjENCgkJCWMtMi4wNDQsMy4xNzktMy4wNjgsOC41NzItMy4wNjgsMTYuMTcyQzQzNC42NTIsMTA2LjcxNCw0MzUuNjE0LDExMi4xODgsNDM3LjUzOCwxMTUuNTI0eiIvPg0KCQk8cGF0aCBmaWxsPSIjMTIzMjM5IiBkPSJNNTEyLjI3LDY2LjY0M2M0LjQyOSwwLDkuNjUxLDAuNTg2LDE1LjY2NCwxLjc1NGwzLjEzOCwwLjYyN2wtMC41MDIsMTAuNzc5DQoJCQljLTYuNTk5LTAuNjY3LTExLjQ4Ny0xLjAwMS0xNC42NjYtMS4wMDFjLTYuMzUzLDAtMTAuNjE0LDEuNDIyLTEyLjc3OCw0LjI2MWMtMi4xNzUsMi44NDUtMy4yNjMsOC4xOS0zLjI2MywxNi4wNDQNCgkJCWMwLDcuODUzLDEuMDQ2LDEzLjI4NSwzLjEzMiwxNi4zYzIuMDkxLDMuMDAyLDYuNDQxLDUuNjMyLDEzLjAzNCw1LjM4MWM4LjcyMi0wLjMzMiwxNC42NjYtMC45OTksMTQuNjY2LTAuOTk5bDAuMzc3LDEwLjkwNA0KCQkJYy04LjQzOSwxLjQ5OC0xNC43ODYsMS4zNzgtMTkuMDQ4LDEuMzc4Yy05LjUyNiwwLTE2LjIzNC0yLjU3LTIwLjExOS03Ljcxcy01LjgyOS0xMy41NTYtNS44MjktMjUuMjU0DQoJCQljMC0xMS42OTYsMi4wNDktMjAuMDMzLDYuMTQzLTI1LjAwNUM0OTYuMzEyLDY5LjEyOSw1MDIuOTk0LDY2LjY0Myw1MTIuMjcsNjYuNjQzeiIvPg0KCQk8cGF0aCBmaWxsPSIjMTIzMjM5IiBkPSJNNTU2LjI3MywxMzAuNjkzaC0xMy42NjJ2LTg3LjExaDEzLjY2MnY0OS42MzVsNy43Ny0wLjc1MWwxNC43OTEtMjQuNDQ2aDE1LjI5M2wtMTguMDQ5LDI5LjMzNA0KCQkJbDE5LjA1MywzMy4zMzhoLTE1LjQxOWwtMTUuMjkzLTI2LjQ0NmwtOC4xNDYsMC44NzZWMTMwLjY5M3oiLz4NCgkJPHBhdGggZmlsbD0iIzEyMzIzOSIgZD0iTTY0NS44NzIsODAuOTMzYy05Ljg2MS0xLjMzNi0xNy4wMDQtMi4wMDUtMjEuNDMyLTIuMDA1Yy00LjQyOSwwLTcuNTAzLDAuNTIzLTkuMjEzLDEuNTcxDQoJCQljLTEuNzE1LDEuMDQxLTIuNTY3LDIuNjg4LTIuNTY3LDQuOTQzYzAsMi4yNjIsMC45NDEsMy44NDksMi44MTgsNC43NjRjMS44NzcsMC45MjMsNi4zMTEsMS45ODYsMTMuMjg1LDMuMTk5DQoJCQljNi45NzYsMS4yMTYsMTEuOTMyLDMuMTE0LDE0Ljg1NCw1LjcwMmMyLjkyMywyLjU5Myw0LjM4Nyw3LjE5MSw0LjM4NywxMy43OWMwLDYuNi0yLjExMiwxMS40NDgtNi4zMzIsMTQuNTM4DQoJCQljLTQuMjE0LDMuMDkzLTEwLjM4NCw0LjYzNi0xOC40ODIsNC42MzZjLTUuMDk4LDAtOC44NDEsMC4yNjQtMjMuMTk2LTEuMzY3bDAsMGwwLjUwOC0xMS41ODcNCgkJCWMxMC4wMywxLjMzNiwxNy45NzgsMS42MzIsMjIuNDY0LDEuMjU1YzQuNDEzLTAuMzcxLDYuODAyLTEuMDA0LDguNjg1LTIuMDkxYzEuODgyLTEuMDgsMi44MTgtMi44NzQsMi44MTgtNS4zODQNCgkJCWMwLTIuNTA3LTAuODk5LTQuMjQtMi42OTMtNS4yMDVjLTEuODA0LTAuOTYtNi4xMDItMi0xMi45MTQtMy4xMjljLTYuODA4LTEuMTM1LTExLjgwMS0yLjkwMi0xNC45NzUtNS4zMzENCgkJCWMtMy4xODItMi40MjEtNC43NjYtNi44NDktNC43NjYtMTMuMjg1YzAtNi40MzQsMi4xODktMTEuMjM5LDYuNTg1LTE0LjQxNWM0LjM4Mi0zLjE3NywxMC4wMDMtNC43NjQsMTYuODUyLTQuNzY0DQoJCQljNS4zNDksMCwxMS45MTEsMC42NzIsMTkuNjgsMi4wMDVsMy44ODUsMC43NTNMNjQ1Ljg3Miw4MC45MzN6Ii8+DQoJPC9nPg0KCTxnPg0KCQk8Zz4NCgkJCTxnPg0KCQkJCTxwb2x5bGluZSBmaWxsPSIjMkM4MzlBIiBwb2ludHM9Ijc1LjQ4LDU4LjM5MiA3NS40OCwwIC0wLjAwNSw0My41OCA1MC41NzIsNzIuNzc2IDc1LjQ4LDU4LjM5MiAJCQkJIi8+DQoJCQkJPHBvbHlsaW5lIGZpbGw9IiM0MEIyOTkiIHBvaW50cz0iNTAuNTY2LDcyLjc3OSA1MC41NzIsNzIuNzc2IC0wLjAwNSw0My41OCAtMC4wMDUsODcuMTYgNTAuNTY2LDg3LjE2IDUwLjU2Niw3Mi43NzkgCQkJCSIvPg0KCQkJCTxwb2x5bGluZSBmaWxsPSIjMUZCMEU2IiBwb2ludHM9IjEwMC4zOTEsNzIuNzc5IDEwMC4zOTEsODcuMTYgMTUwLjk2MSw4Ny4xNiAxNTAuOTYxLDQzLjU4IDEwMC4zOTEsNzIuNzc2IDEwMC4zOTEsNzIuNzc5IAkJCQkNCgkJCQkJIi8+DQoJCQkJPHBvbHlsaW5lIGZpbGw9IiM2Q0NFRjUiIHBvaW50cz0iMTAwLjM5MSw3Mi43NzYgMTUwLjk2MSw0My41OCA3NS40OCwwIDc1LjQ4LDU4LjM5MiAxMDAuMzkxLDcyLjc3NiAJCQkJIi8+DQoJCQkJPHBvbHlnb24gZmlsbD0iI0JCRDYzMSIgcG9pbnRzPSI1MC41NjYsODcuMTYgLTAuMDA1LDg3LjE2IC0wLjAwNSwxMzAuNzM3IDUwLjU2NiwxMDEuNTM4IAkJCQkiLz4NCgkJCQk8cG9seWxpbmUgZmlsbD0iIzQ5Qjc0OSIgcG9pbnRzPSI1MC41NjYsMTAxLjU0MSA1MC41NjYsMTAxLjUzOCAtMC4wMDUsMTMwLjczNyA3NS40OCwxNzQuMzE5IDc1LjQ4LDExNS45MjQgNTAuNTY2LDEwMS41NDEgDQoJCQkJCQkJCQkiLz4NCgkJCQk8cG9seWxpbmUgZmlsbD0iIzE5OTNEMiIgcG9pbnRzPSIxMDAuMzkxLDg3LjE2IDEwMC4zOTEsMTAxLjU0MSA3NS40OCwxMTUuOTI0IDc1LjQ4LDE3NC4zMTkgMTUwLjk2MSwxMzAuNzM3IDE1MC45NjEsODcuMTYgDQoJCQkJCTEwMC4zOTEsODcuMTYgCQkJCSIvPg0KCQkJCTxwb2x5bGluZSBmaWxsPSIjRkZGRkZGIiBwb2ludHM9Ijc1LjQ4LDg3LjE2IDc1LjQ4LDg3LjE1NyA3NS40NzYsODcuMTYgNzUuNDgsODcuMTYgCQkJCSIvPg0KCQkJCTxwb2x5bGluZSBmaWxsPSIjQzVDN0M5IiBwb2ludHM9Ijc1LjQ4LDU4LjM5MiA3NS40OCw1OC4zOTIgNTAuNTcyLDcyLjc3NiA3NS40NzYsODcuMTYgNzUuNDgsODcuMTU3IDc1LjQ4LDU4LjM5MiAJCQkJIi8+DQoJCQkJPHBvbHlsaW5lIGZpbGw9IiNGMEYxRjEiIHBvaW50cz0iNzUuNDgsNTguMzkyIDc1LjQ4LDg3LjE1NyAxMDAuMzkxLDcyLjc3NiA3NS40OCw1OC4zOTIgCQkJCSIvPg0KCQkJCTxwb2x5bGluZSBmaWxsPSIjRENERERFIiBwb2ludHM9IjUwLjU3Miw3Mi43NzYgNTAuNTcyLDcyLjc3NiA1MC41NjYsNzIuNzc5IDUwLjU2Niw4Ny4xNiA3NS40NzYsODcuMTYgNTAuNTcyLDcyLjc3NiAJCQkJIi8+DQoJCQkJPHBvbHlsaW5lIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNzUuNDgsODcuMTYgNzUuNDgsODcuMTYgNzUuNDc2LDg3LjE2IDc1LjQ4LDg3LjE2IAkJCQkiLz4NCgkJCQk8cG9seWxpbmUgZmlsbD0iI0RFREZFMSIgcG9pbnRzPSIxMDAuMzkxLDcyLjc3NiAxMDAuMzkxLDcyLjc3NiA3NS40OCw4Ny4xNTcgNzUuNDgsODcuMTYgMTAwLjM5MSw4Ny4xNiAxMDAuMzkxLDcyLjc3OSANCgkJCQkJMTAwLjM5MSw3Mi43NzYgCQkJCSIvPg0KCQkJCTxwb2x5bGluZSBmaWxsPSIjQ0JDRENGIiBwb2ludHM9IjEwMC4zOTEsODcuMTYgNzUuNDgsODcuMTYgNzUuNDgsODcuMTYgNzUuNDgsMTE1LjkyNCAxMDAuMzkxLDEwMS41NDEgMTAwLjM5MSw4Ny4xNiAJCQkJIi8+DQoJCQkJPHBvbHlsaW5lIGZpbGw9IiNFQkVDRUQiIHBvaW50cz0iNzUuNDc2LDg3LjE2IDUwLjU2Niw4Ny4xNiA1MC41NjYsMTAxLjUzOCA3NS40NzYsODcuMTYgCQkJCSIvPg0KCQkJCTxwb2x5bGluZSBmaWxsPSIjRDlEQURDIiBwb2ludHM9Ijc1LjQ3Niw4Ny4xNiA3NS40NzYsODcuMTYgNTAuNTY2LDEwMS41MzggNTAuNTY2LDEwMS41NDEgNzUuNDgsMTE1LjkyNCA3NS40OCw4Ny4xNiANCgkJCQkJNzUuNDc2LDg3LjE2IAkJCQkiLz4NCgkJCTwvZz4NCgkJPC9nPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K">'
                . '<div id="payment_desc">'
                . '  Pay with Raiblocks (using brainblocks.io)'
                . '</div>'
        ];
        $installer->createOrUpdate($plugin, $options);
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        $this->setPaymentActiveFlag($context, false);
    }

    /**
     * @param DeactivateContext $context
     */
    public function deactivate(DeactivateContext $context)
    {
        $this->setPaymentActiveFlag($context, false);
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $this->setPaymentActiveFlag($context, true);
    }

    /**
     * @param InstallContext $context
     * @param bool $active
     */
    private function setPaymentActiveFlag(InstallContext $context, $active)
    {
        $em = $this->container->get('models');
        $payments = $context->getPlugin()->getPayments();
        foreach ($payments as $payment) {
            $payment->setActive($active);
        }

        $em->flush();
    }
}
