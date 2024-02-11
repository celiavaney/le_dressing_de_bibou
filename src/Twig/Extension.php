<?php 

namespace App\Twig;

use Twig\TwigTest;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\Articles;
use Twig\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Extension extends AbstractExtension{
    private $parameters; 
    public function __construct(ParameterBagInterface $parameters){
        $this->parameters = $parameters;
    }

    // public function sexe(Articles $articles): string {
    //     $text = "";
    //     foreach($articles->getSexe() as $sexe){
    //         $text .= $text  ? ", " : "";
    //         switch($sexe){
    //             case "fille" :
    //                 $text .= "fille";
    //                 break;
    //             case "garçon" :
    //                 $text .= "garçon";
    //                 break;
    //             case "unisexe" :
    //                 $text .= "unisexe";
    //                 break;
    //             default:
    //                 $text .= "Autre";
    //                 break;
    //         }
    //     }
    //     return $text;
    // }

    // public function getFilters(){
    //     return  [
    //         new TwigFilter("sexes", [$this, "sexe"]),
    //     ];
    // }
}