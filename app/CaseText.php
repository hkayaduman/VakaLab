<?php
declare(strict_types=1);

final class CaseText {
    public static function fromEditor(mixed $value,string $label='Vaka metni'): array {
        demand(is_string($value),$label.' geçersiz');
        $document=new DOMDocument();
        $document->loadHTML('<!doctype html><html><head><meta charset="UTF-8"></head><body>'.$value.'</body></html>',
            LIBXML_NONET|LIBXML_NOERROR|LIBXML_NOWARNING);
        $text='';
        $html=self::children($document->getElementsByTagName('body')->item(0),$text);
        return ['body'=>trim($text),'body_html'=>$html];
    }

    private static function children(DOMNode $parent,string &$text): string {
        $html='';
        foreach($parent->childNodes as $node){
            if($node instanceof DOMText){
                $content=str_replace("\u{00a0}",' ',$node->data);
                $text.=$content;
                $html.=htmlspecialchars($content,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
                continue;
            }
            if(!$node instanceof DOMElement)continue;
            $tag=strtolower($node->tagName);
            if(in_array($tag,['script','style','template','iframe','object','svg','math'],true))continue;
            if($tag==='br'){
                $text.="\n";
                $html.='<br>';
                continue;
            }
            $tag=match($tag){'b'=>'strong','i'=>'em',default=>$tag};
            $block=in_array($tag,['div','p','ul','li'],true);
            if($block&&$text!==''&&!str_ends_with($text,"\n"))$text.="\n";
            $children=self::children($node,$text);
            if($block&&!str_ends_with($text,"\n"))$text.="\n";
            $html.=$block||in_array($tag,['strong','em','u'],true)?"<$tag>$children</$tag>":$children;
        }
        return $html;
    }
}
