<?php

namespace Humbrain\Framework\extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('field', [$this, 'field'], ['is_safe' => ['html'], 'needs_context' => true])
        ];
    }

    /**
     * @param array $context
     * @param string $name
     * @param string $label
     * @param string|null $value
     * @param string $type
     * @param array $options
     * @return string
     */
    public function field(
        array $context,
        string $name,
        string $label,
        mixed $value = null,
        string $type = 'text',
        array $options = []
    ): string {
        $error = $context['errors'] ?? [];
        $classDiv = ['form-group'];
        $classInput = ['form-control'];
        $value = htmlspecialchars($value);
        list($classDiv[], $classInput[], $errorDiv) = $this->returnErrorDiv($name, $error);
        $classDiv = implode(' ', $classDiv);
        $classInput = implode(' ', $classInput);
        $field = match ($type) {
            'textarea' => $this->returnTextarea($name, $label, $value, $classInput),
            'select' => $this->returnSelect($name, $label, $value, $options['options'], $classInput),
            default => $this->returnInput($name, $label, $type, $value, $classInput),
        };
        $label = $type != 'hidden' ? "<label for='$name'>$label</label>" : '';
        return "<div class='$classDiv'>
                    $label
                    $field
                    $errorDiv
                </div>";
    }

    /**
     * @param string $name
     * @param array $error
     * @return string[]
     */
    private function returnErrorDiv(string $name, array $error): array
    {
        $classDiv = '';
        $classInput = '';
        $errorDiv = '';
        if ($error) :
            $classDiv = 'has-validation';
            if (isset($error[$name])) :
                $classInput = 'is-invalid';
                $errorDiv = "<div class=\"invalid-feedback\" style='display: block'>{$error[$name]}!</div>";
            else :
                $classInput = 'is-valid';
            endif;
        endif;
        return [$classDiv, $classInput, $errorDiv];
    }

    /**
     * @param string $name
     * @param string $label
     * @param string|null $value
     * @param string $classInput
     * @return string
     */
    private function returnTextarea(string $name, string $label, mixed $value, string $classInput): string
    {
        return "<textarea name='$name' class='$classInput' id='$name' placeholder='$label'>$value</textarea>";
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $value
     * @param array $options
     * @param string $classInput
     * @return string
     */
    private function returnSelect(string $name, string $label, mixed $value, array $options, string $classInput): string
    {
        $options_html = '<option value="">Select</option>';
        foreach ($options as $key => $option) :
            $selected = $key == $value ? 'selected' : '';
            $options_html .= "<option value='{$key}' $selected>{$option}</option>";
        endforeach;
        return "<select name='$name' class='$classInput' id='$name' placeholder='$label'>
                    $options_html
                </select>";
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $type
     * @param string|null $value
     * @param string $classInput
     * @return string
     */
    private function returnInput(string $name, string $label, string $type, mixed $value, string $classInput): string
    {
        return "<input type='$type' name='$name' class='$classInput' id='$name' placeholder='$label' value='$value'>";
    }
}
