MageProfis WidgetArray Extension
=====================
Dynamic widget fields

Facts
-----
- version: 1.0.0
- [extension on GitHub](https://github.com/mageprofis/MageProfis_WidgetArray)
- [direct download link](http://connect.magentocommerce.com/community/get/MageProfis_WidgetArray-1.0.0.tgz)

Description
-----------
This is not a widget itself, but a useful addition to the Magento widget ecosystem. By installing this extension you can use dynamic fields within your own widgets.

Usage
-----
In your `widget.xml` you can use this extension as a helper block:
```xml
<!-- Widget XML defintion -->
            <items>
                <visible>1</visible>
                <label>Items</label>
                <type>array</type> <!-- Use "array" type -->
                <helper_block>
                    <type>mp_widgetarray/adminhtml_widget_array</type>
                    <data>
                        <columns>
                            <text translate="label">
                                <label>Text</label>
                            </text>
                            <col2 translate="label">
                                <label>Col 2</label>
                            </col2>
                        </columns>
                    </data>
                </helper_block>
            </items>
<!-- Widget XML defintion -->
```

The values are stored as a base64 __AND__ json_encoded string, so in your widget block class you need to use `base64_decode` and `json_decode`.

```php
<?php

class Namespace_Module_Block_Widget_YourWidget
extends Mage_Core_Block_Template
implements Mage_Widget_Block_Interface
{
    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        $b64 = $this->getData('items');
        $json = base64_decode($b64);

        return json_decode($json, true);
    }
```

###### The result
The result will look similar to this, depending on the columns you've configured in your `widget.xml`:

![Widget usage example](https://cloud.githubusercontent.com/assets/568497/14742831/7b2fcd76-089e-11e6-9235-f92f9296ed6c.png)

Compatibility
-------------
- Magento >= 1.9 (untested, but should work in 1.7 as well)

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/mageprofis/MageProfis_WidgetArray/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Volker Thiel

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2016 MageProfis
