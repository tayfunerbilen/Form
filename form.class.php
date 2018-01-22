<?php

namespace Prototurk;

trait HtmlElements {

    public function start($action, $method = 'post')
    {
        $this->action = $action;
        $this->method = $method;
        return $this->html = '<form action="' . $this->action . '" method="' . $this->method . '">';
    }

    public function end()
    {
        $html = '</form>';
        $this->html .= $html;
        return $html;
    }

    public function input($name, $attributes, $defaultValue = '')
    {
        $this->inputs[$name] = [
            'attributes' => $attributes,
            'defaultValue' => $defaultValue,
            'type' => 'input'
        ];
        $html = '<input' . ($defaultValue ? ' value="' . $defaultValue . '"' : null);
        $html .= $this->setAttributes($name, $attributes);
        $html .= '>';
        $this->html .= $html;
        return $html;
    }

    public function textarea($name, $attributes, $defaultValue = '')
    {
        $this->inputs[$name] = [
            'attributes' => $attributes,
            'defaultValue' => $defaultValue,
            'type' => 'textarea'
        ];
        $html = '<textarea';
        $html .= $this->setAttributes($name, $attributes);
        $html .= '>' . $defaultValue . '</textarea>';  
        $this->html .= $html;
        return $html;
    }

    public function select($name, $options, $attributes, $defaultValue = '')
    {
        $this->inputs[$name] = [
            'attributes' => $attributes,
            'defaultValue' => $defaultValue,
            'options' => $options,
            'type' => 'select'
        ];
        $html = '<select';
        $html .= $this->setAttributes($name, $attributes);
        $html .= '>';
        foreach ($options as $key => $val){
            $html .= '<option ' . ($defaultValue == $key ? ' selected' : null) . ' value="' . $key . '">' . $val . '</option>';
        }
        $html .= '</select>';
        $this->html .= $html;
        return $html;
    }
    
    public function button($text, $id = null, $class = 'btn')
    {
        $this->buttons[$text] = [
            'id' => $id,
            'class' => $class
        ];
        $button = '<button name="submit" value="1" type="submit" ' . ($id ? ' id="' . $id . '"' : null) . ' class="' . $class . '">' . $text . '</button>';
        $this->html .= $button;
        return $button;
    }

    public function label($text, $id = null)
    {
        $this->labels[$text] = $id;
        $label = '<label' . ($id ? ' for="' . $id . '"' : null) . '>' . $text . '</label>';
        $this->html .= $label;
        return $label;
    }

}

class Form {
    
    use HtmlElements;

    protected $html;
    public $action;
    public $method;
    public $inputs = [];
    public $buttons = [];
    public $labels = [];
    protected $values = [];
    protected $errors = [];

    public function setAttributes($name, $attributes)
    {
        $html = ' name="' . $name . (isset($attributes['multiple']) ? '[]' : null) . '"';
        foreach ($attributes as $key => $val){
            $html .= ' ' . $key . '="' . $val . '"';
        }
        return $html;
    }

    public function show($templateName = null)
    {
        if ($templateName){
            ob_start();
            require __DIR__ . '/' . $templateName . '.html';
            $output = ob_get_clean();
            
            $output = str_replace(
                ['{form}', '{/form}'],
                [$this->start($this->action, $this->method), $this->end()],
                $output
            );

            foreach ($this->labels as $text => $id)
            {
                $output = str_replace(
                    '{label="' . $id . '"}',
                    $this->label($text, $id),
                    $output
                );
            }

            foreach ($this->buttons as $text => $args)
            {
                $output = str_replace(
                    '{button="' . $args['id'] . '"}',
                    $this->button($text, $args['id'], $args['class']),
                    $output
                );
            }

            foreach ($this->inputs as $name => $args)
            {
                if ($args['type'] == 'input'){
                    $output = str_replace(
                        '{input="' . $name . '"}',
                        $this->input($name, $args['attributes'], $args['defaultValue']),
                        $output
                    );
                }
                if ($args['type'] == 'textarea'){
                    $output = str_replace(
                        '{textarea="' . $name . '"}',
                        $this->textarea($name, $args['attributes'], $args['defaultValue']),
                        $output
                    );
                }
                if ($args['type'] == 'select'){
                    $output = str_replace(
                        '{select="' . $name . '"}',
                        $this->select($name, $args['options'], $args['attributes'], $args['defaultValue']),
                        $output
                    );
                }
            }

            return $output;

        } else {
            return $this->html;
        }
    }

    public function control()
    {
        $posts = $_POST;
        foreach ($this->inputs as $name => $args){
            if (isset($args['attributes']['required'])){
                if (!isset($posts[$name]) || empty($posts[$name])){
                    $this->errors[] = $name . ' boş bırakılamaz!';
                } else {
                    $this->values[$name] = $posts[$name];
                }
            } else {
                if (isset($posts[$name]) && !empty($posts[$name]))
                    $this->values[$name] = $posts[$name];
            }
        }
        return count($this->errors) > 0 ? false : true;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function values()
    {
        return $this->values;
    }

    public function dump()
    {
        print_r($this->inputs);
    }

}
