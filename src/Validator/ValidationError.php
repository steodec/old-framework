<?php

namespace Humbrain\Framework\Validator;

class ValidationError
{
    const MESSAGE = [
        "required" => "Le champ %s est requis",
        "empty" => "Le champ %s ne peut être vide",
        "slug" => "Le champ %s n'est pas un slug valide",
        "minLength" => "Le champ %s doit contenir plus de %d caractères",
        "maxLength" => "Le champ %s doit contenir moins de %d caractères",
        "betweenLength" => "Le champ %s doit contenir entre %d et %d caractères",
        "datetime" => "Le champ %s doit être une date valide (%s)",
        "exists" => "La valeur du champ %s (%s) n'existe pas dans la table",
        "unique" => "%s a déjà une entrée dans la table avec cette valeur (%s)",
    ];
    private string $key;
    private string $rule;

    private array $attributes = [];

    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString(): string
    {
        return sprintf(self::MESSAGE[$this->rule], ...array_merge([$this->key], $this->attributes));
    }
}
