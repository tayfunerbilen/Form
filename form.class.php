<?php

namespace Prototurk;

trait HtmlElements {

    public function start($url = '', $method = 'post', $className = '')
    {
        $this->method = $method;
        $this->url = $url;
        return $this->html = '<form action="' . $this->url . '" method="' . $this->method . '"' . ($className ? ' class="' . $className . '"' : null) . '>';
    }

    public function end()
    {
        $form = '</form>';
        $this->html .= $form;
        return $form;
    }

    public function input($name, $attributes = [], $defaultValue = '')
    {
        $this->inputs[$name] = [
            'attributes' => $attributes,
            'defaultValue' => $defaultValue,
            'type' => 'input'
        ];
        $input = '<input type="' . ($attributes['type'] ?? 'text') . '"';
        if ($defaultValue){
            $input .= ' value="' . $defaultValue . '"';
        }
        $input .= $this->setAttributes($attributes, $name);
        $input .= '>';
        $this->html .= $input;
        return $input;
    }

    public function textarea($name, $attributes = [], $defaultValue = '')
    {
        $this->inputs[$name] = [
            'attributes' => $attributes,
            'defaultValue' => $defaultValue,
            'type' => 'textarea'
        ];
        $textarea = '<textarea';
        $textarea .= $this->setAttributes($attributes, $name);
        $textarea .= '>' . $defaultValue . '</textarea>';
        $this->html .= $textarea;
        return $textarea;
    } 

    public function select($name, $options, $attributes = [], $defaultValue = '')
    {
        $this->inputs[$name] = [
            'options' => $options,
            'attributes' => $attributes,
            'defaultValue' => $defaultValue,
            'type' => 'select'
        ];
        $select = '<select';
        $select .= $this->setAttributes($attributes, $name);
        $select .= '>';
        foreach ($options as $key => $val){
            $select .= '<option' . ($defaultValue == $key ? ' selected' : null) . ' value="' . $key . '">' . $val . '</option>';
        }
        $select .= '</select>';
        $this->html .= $select;
        return $select;
    }

    public function label($text, $id = null)
    {
        $this->labels[$text] = $id;
        $label = '<label' . ($id ? ' for="' . $id . '"' : null) . '>' . $text . '</label>';
        $this->html .= $label;
        return $label;
    }

    public function button($text, $class = 'btn')
    {
        $this->buttons[$text] = $class;
        $button = '<button type="submit" name="submit" value="1" class="' . $class . '">' . $text . '</button>';
        $this->html .= $button;
        return $button;
    }

}

class Form {

    use HtmlElements;

    public $html;
    public $method;
    public $url;
    public $buttons = [];
    public $inputs = [];
    public $labels = [];
    public $errors = [];
    public $values = [];

    public function setAttributes($attributes, $name)
    {
        $html = ' name="' . $name . (isset($attributes['multiple']) ? '[]' : null) . '"';
        foreach ($attributes as $attr => $value){
            $html .= ' ' . $attr . '="' . $value . '"';
        }
        return $html;
    }

    public function control()
    {
        $missings = [];
        $posts = $_POST;
        foreach ($this->inputs as $name => $args){
            if (isset($args['attributes']['required'])){
                if (!isset($posts[$name]) || empty(trim($posts[$name]))){
                    $this->errors[] = ($args['attributes']['placeholder'] ?? $name) . ' eksik, lütfen doldurun.';
                } else {
                    $this->values[$name] = $posts[$name];
                }
            } else {
                if (isset($posts[$name]))
                    $this->values[$name] = $posts[$name];
            }
        }
        return count($this->errors) > 0 ? false : true;
    }

    public function values()
    {
        return $this->values;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function show($templateName = null)
    {
        if ($templateName){
            ob_start();
                include __DIR__ . '/' . $templateName . '.template';
            $output = ob_get_clean();
            $output = str_replace(
                ['{form}', '{/form}'],
                [$this->start(), $this->end()],
                $output
            );
            foreach ($this->inputs as $name => $args) {
                if ($args['type'] == 'input'){
                    $output = str_replace('{input="' . $name . '"}', $this->input($name, $args['attributes'], $args['defaultValue']), $output);
                }
                elseif ($args['type'] == 'textarea'){
                    $output = str_replace('{textarea="' . $name . '"}', $this->textarea($name, $args['attributes'], $args['defaultValue']), $output);
                }
                elseif ($args['type'] == 'select'){
                    $output = str_replace('{select="' . $name . '"}', $this->select($name, $args['options'], $args['attributes'], $args['defaultValue']), $output);
                }
            }
            foreach ($this->labels as $text => $id){
                $output = str_replace('{label="' . $text . '"}', $this->label($text, $id), $output);
            }
            foreach ($this->buttons as $text => $class){
                $output = str_replace('{button="' . $text . '"}', $this->button($text, $class), $output);
            }
            return $output;
        } else {
            return $this->html;
        }
    }

    public function dump()
    {
        print_r($this->inputs);
    }

}
