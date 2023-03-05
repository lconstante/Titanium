<?php
class Control
{
    private string $type;
    private array $properties;
    private array $childs=array();
    private string $content="";

    public function __construct($type, $properties = array(), $content = "")
    {
        $this->type = $type;
        $this->properties = $properties;
        $this->content=$content;
    }

    public function getId()
    {
        $id = isset($this->properties["id"]) ? $this->properties["id"] : "";
        return $id;
    }

    public function addChild($child)
    {
        $this->childs[count($this->childs)]=$child;
    }

    private function getProperties()
    {
        $presentar = "";
        foreach($this->properties as $key => $value)
        {
            $presentar .= " ";
            $presentar .= $key;
            $presentar .= $value =="" ? "" : " = \"$value\"";
        }
        return $presentar;
    }

    private function getContent()
    {
        return $this->content;
    }

    private function getChilds($childs)
    {
        $presentar="";
        foreach($childs as $indice => $child)
        {
            if (is_object($child))
            {
                $presentar .= "<";
                $presentar .= $child->type;
                $presentar .= $child->getProperties();
                $presentar .= ">";
                $presentar .= $child->getContent();
                $presentar .= $this->getChilds($child->childs);
                $presentar .= "</";
                $presentar .= $child->type;
                $presentar .= ">";
            }
            elseif (is_string($child))
            {
                $presentar .= $child;
            }
        }
        return $presentar;
    }

    public function mostrar()
    {
        $presentar="";

        $presentar .= "<";
        $presentar .= $this->type;
        $presentar .= $this->getProperties();
        $presentar .= ">";
        $presentar .= $this->getContent();
        $presentar .= $this->getChilds($this->childs);
        $presentar .= "</";
        $presentar .= $this->type;
        $presentar .= ">";

        return $presentar;
    }

}
?>