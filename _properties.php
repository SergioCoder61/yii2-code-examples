<?php
use yii\helpers\ArrayHelper;
?>


        <table class="properties">
            <?php
            $groupNames = array_unique(ArrayHelper::getColumn($articleProperties, 'group_name'));
            foreach ($groupNames as $groupName) {
                $properties = [];
                foreach ($articleProperties as $property) {
                    if ($property['group_name'] == $groupName) {
                        $properties[] = $property;
                    }
                }

                $propertyNames = array_unique(ArrayHelper::getColumn($properties, 'property_name'));

                echo "
<tr class='group'>
    <td colspan='2'>${groupName}</td>
</tr>
            ";

                foreach ($propertyNames as $propertyName) {
                    echo "
<tr>
    <td class='property_name'>${propertyName}</td>
    <td>
";
                    $propertyValues = [];
                    foreach ($properties as $property) {
                        if ($property['property_name'] == $propertyName) {
                            $propertyValues[] = $property;
                        }
                    }

                    $propertyValue = $propertyValues[0];

                    // SomeOf, OneOf, yesNoUnknown
                    if ($propertyValue['property_type_id'] == 5 || $propertyValue['property_type_id'] == 6) {
                        $values = ArrayHelper::getColumn($propertyValues, 'property_valid_value');
                        echo implode(', ', $values);
                    } elseif ($propertyValue['property_type_id'] == 8) {
                        if ($propertyValue['property_value'] == 1) {
                            echo 'да';
                        }
                        if ($propertyValue['property_value'] == 0) {
                            echo 'нет';
                        }
                    } else {
                        $propertyUnitName = $propertyValue['property_unit_name'] === null ? '' : $propertyValue['property_unit_name'];
                        echo $propertyValue['property_value'] . ' ' . $propertyUnitName;
                    }

                    echo '
    </td>
</tr>
                ';
                }
            }
            ?>
        </table>
