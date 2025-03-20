<?php

declare(strict_types=1);

namespace M2E\Temu\Model;

class Settings
{
    public const VALUE_MODE_NOT_SET = 0;
    public const VALUE_MODE_ATTRIBUTE = 1;

    public const PACKAGE_MODE_NOT_SET          = 0;
    public const PACKAGE_MODE_CUSTOM_ATTRIBUTE = 1;
    public const PACKAGE_MODE_CUSTOM_VALUE     = 2;

    public const DIMENSION_TYPE_WIDTH  = 'width';
    public const DIMENSION_TYPE_LENGTH = 'length';
    public const DIMENSION_TYPE_HEIGHT = 'height';
    public const DIMENSION_TYPE_WEIGHT = 'weight';

    public const CONFIG_GROUP = '/module/configuration/';
    public const CONFIG_KEY_IDENTIFIER_CODE_MODE = 'identifier_code_mode';
    public const CONFIG_KEY_IDENTIFIER_CODE_CUSTOM_ATTRIBUTE = 'identifier_code_custom_attribute';

    private \M2E\Temu\Model\Config\Manager $config;

    public function __construct(\M2E\Temu\Model\Config\Manager $config)
    {
        $this->config = $config;
    }

    //region Identifier

    public function isIdentifierCodeConfigured(): bool
    {
        return $this->getIdentifierCodeMode() === self::VALUE_MODE_ATTRIBUTE;
    }

    public function setIdentifierCodeMode(int $mode): void
    {
        $this->validateMode($mode);

        $this->config->set(self::CONFIG_GROUP, self::CONFIG_KEY_IDENTIFIER_CODE_MODE, $mode);
    }

    public function getIdentifierCodeMode(): int
    {
        return (int)$this->config->get(self::CONFIG_GROUP, self::CONFIG_KEY_IDENTIFIER_CODE_MODE);
    }

    public function setIdentifierCodeValue(string $value): void
    {
        $this->config->set(
            self::CONFIG_GROUP,
            self::CONFIG_KEY_IDENTIFIER_CODE_CUSTOM_ATTRIBUTE,
            $value
        );
    }

    public function getIdentifierCodeValue(): string
    {
        return (string)$this->config->get(self::CONFIG_GROUP, self::CONFIG_KEY_IDENTIFIER_CODE_CUSTOM_ATTRIBUTE);
    }

    //endregion

    public function setConfigValues(array $values): void
    {
        if (isset($values['identifier_code_mode'])) {
            $this->setIdentifierCodeMode((int)$values['identifier_code_mode']);
        }

        if (isset($values['identifier_code_custom_attribute'])) {
            $this->setIdentifierCodeValue((string)$values['identifier_code_custom_attribute']);
        }

        $dimensionTypes = [
            self::DIMENSION_TYPE_WIDTH,
            self::DIMENSION_TYPE_HEIGHT,
            self::DIMENSION_TYPE_LENGTH,
            self::DIMENSION_TYPE_WEIGHT,
        ];

        foreach ($dimensionTypes as $packageDimension) {
            //region Set Mode
            $modeKey = $this->makeModeKey($packageDimension);
            $mode = $values[$modeKey] ?? 0;
            $this->config->set(self::CONFIG_GROUP, $modeKey, $mode);
            //endregion

            //region Set Custom Attribute
            $customAttributeKey = $this->makeCustomAttributeKey($packageDimension);
            $customAttribute = $values[$customAttributeKey] ?? '';
            if ($mode != self::PACKAGE_MODE_CUSTOM_ATTRIBUTE) {
                $customAttribute = '';
            }
            $this->config->set(self::CONFIG_GROUP, $customAttributeKey, $customAttribute);
            //endregion

            //region Set Custom Value
            $customValueKey = $this->makeCustomValueKey($packageDimension);
            $customValue = $values[$customValueKey] ?? '';
            if ($mode != self::PACKAGE_MODE_CUSTOM_VALUE) {
                $customValue = '';
            }
            $this->config->set(self::CONFIG_GROUP, $customValueKey, $customValue);
            //endregion
        }
    }

    // ----------------------------------------

    private function validateMode(int $mode): void
    {
        if (!in_array($mode, [self::VALUE_MODE_NOT_SET, self::VALUE_MODE_ATTRIBUTE])) {
            throw new \InvalidArgumentException('Invalid identifier code mode.');
        }
    }

    // ----------------------------------------

    public function getPackageDimensionMode(string $dimensionType): int
    {
        return (int)$this->config->get(
            self::CONFIG_GROUP,
            $this->makeModeKey($dimensionType)
        );
    }

    public function isPackageDimensionModeNotSet(string $dimensionType): bool
    {
        return
            (int)$this->config->get(
                self::CONFIG_GROUP,
                $this->makeModeKey($dimensionType)
            ) === self::PACKAGE_MODE_NOT_SET;
    }

    public function getPackageDimensionCustomAttribute(string $dimensionType): string
    {
        return (string)$this->config->get(
            self::CONFIG_GROUP,
            $this->makeCustomAttributeKey($dimensionType)
        );
    }

    public function getPackageDimensionCustomValue(string $dimensionType): string
    {
        return (string)$this->config->get(
            self::CONFIG_GROUP,
            $this->makeCustomValueKey($dimensionType)
        );
    }

    /**
     * Generated:
     * - package_width_mode
     * - package_length_mode
     * - package_height_mode
     * - package_weight_mode
     */
    private function makeModeKey(string $dimensionType): string
    {
        return "package_{$dimensionType}_mode";
    }

    /**
     * Generated:
     * - package_width_custom_attribute
     * - package_length_custom_attribute
     * - package_height_custom_attribute
     * - package_weight_custom_attribute
     */
    private function makeCustomAttributeKey(string $dimensionType): string
    {
        return "package_{$dimensionType}_custom_attribute";
    }

    /**
     * Generated:
     * - package_width_custom_value
     * - package_length_custom_value
     * - package_height_custom_value
     * - package_weight_custom_value
     */
    private function makeCustomValueKey(string $dimensionType): string
    {
        return "package_{$dimensionType}_custom_value";
    }
}
