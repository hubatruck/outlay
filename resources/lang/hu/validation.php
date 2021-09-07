<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute el kell fogadni.',
    'active_url' => ':attribute nem érvényes URL.',
    'after' => ':attribute egy :date utáni dátumnak kell lennie.',
    'after_or_equal' => ':attribute :date után vagy azzal megegyező dátumnak kell lennie.',
    'alpha' => ':attribute csak betűket tartalmazhat.',
    'alpha_dash' => ':attribute csak betűket, számokat, kötőjeleket és aláhúzásokat tartalmazhat.',
    'alpha_num' => ':attribute csak betűket és számokat tartalmazhat.',
    'array' => ':attribute tömbnek kell lennie.',
    'before' => ':attribute egy :date előtti dátumnak kell lennie.',
    'before_or_equal' => ':attribute :date előtti vagy azzal egyenlő dátumnak kell lennie.',
    'between' => [
        'array' => ':attribute :min - :max közötti elemet kell tartalmazzon.',
        'file' => ':attribute csatolmány mérete :min és :max kilobájt között kell lennie.',
        'numeric' => ':attribute értéke :min és :max közötti számnak kell lennie.',
        'string' => ':attribute hossza :min és :max karakter között kell lennie.',
    ],
    'boolean' => ':attribute igaznak vagy hamisnak kell lennie.',
    'confirmed' => 'A megerősítés nem egyezik.',
    'current_password' => 'Érvénytelen jelszó.',
    'date' => ':attribute nem érvényes dátum.',
    'date_equals' => ':attribute :date-nak/nek kell lennie.',
    'date_format' => ':attribute nem felel meg az :format formátumnak.',
    'different' => ':attribute különböznie kell ettől: :other.',
    'digits' => ':attribute :digits számjegynek kell lennie.',
    'digits_between' => ':attribute :min és :max közötti számjegynek kell lennie.',
    'dimensions' => ':attribute érvénytelen méretekkel rendelkezik.',
    'distinct' => ':attribute ismétlődő értékkel rendelkezik.',
    'email' => ':attribute érvényes e-mail címnek kell lennie.',
    'ends_with' => ':attribute az alábbiak egyikével kell végződnie: :values.',
    'exists' => 'A kiválasztott :attribute érvénytelen.',
    'file' => ':attribute fájlnak kell lennie.',
    'filled' => ':attribute kell legyen értéke.',
    'gt' => [
        'array' => ':attribute több, mint :value elemet kell tartalmazzon.',
        'file' => ':attribute csatolmány mérete nagyobbnak kell lennie, mint :value kilobájt.',
        'numeric' => ':attribute értéke többnek kell lennie, mint :value.',
        'string' => ':attribute hossza több mint :value karater lehet.',
    ],
    'gte' => [
        'array' => ':attribute legalább :value elemet kell tartalmazzon.',
        'file' => ':attribute csatolmány mérete legalább :value kilobáj lehet.',
        'numeric' => ':attribute értéke legalább :value lehet.',
        'string' => ':attribute hossza legalább :value karater lehet.',
    ],
    'image' => ':attribute egy kép lehet.',
    'in' => ':attribute értéke érvénytelen.',
    'in_array' => ':attribute értéke nem létezik :other-ban.',
    'integer' => ':attribute értéke egész szám kell legyen.',
    'ip' => ':attribute érvényes IP-cím kell legyen.',
    'ipv4' => ':attribute egy érvényes IPv4 címnek kell lennie.',
    'ipv6' => ':attribute egy érvényes IPv6 címnek kell lennie.',
    'json' => ':attribute egy érvényes JSON karakterlánc lehet.',
    'lt' => [
        'array' => ':attribute kevesebb, mint :value elemet kell tartalmazzon.',
        'file' => ':attribute csatolmány mérete kisebbnek kell lennie, mint :value kilobájt.',
        'numeric' => ':attribute értéke kissebnek kell lennie, mint :value.',
        'string' => ':attribute hossza kevesebb mint :value karater lehet.',
    ],
    'lte' => [
        'array' => ':attribute legfeljebb :value elemet kell tartalmazzon.',
        'file' => ':attribute csatolmány mérete legfeljebb :value kilobájt lehet.',
        'numeric' => ':attribute értéke legtöbb :value lehet.',
        'string' => ':attribute hossza legtöbb :value karater lehet.',
    ],
    'max' => [
        'array' => ':attribute maximum :max elemet tartalmazhat.',
        'file' => ':attribute csatolmány mérete maximum :max kilobájt lehet.',
        'numeric' => ':attribute értéke maximum :max lehet.',
        'string' => ':attribute hossza maximum :max karater lehet.',
    ],
    'mimes' => ':attribute a következő típusú fájlnak kell lennie: :values.',
    'mimetypes' => ':attribute :values típusú fájlnak kell lennie.',
    'min' => [
        'array' => ':attribute minimum :min elemet tartalmazhat.',
        'file' => ':attribute csatolmány mérete minimum :min kilobájt lehet.',
        'numeric' => ':attribute értéke minimum :min lehet.',
        'string' => ':attribute hossza minimum :min karater lehet.',
    ],
    'multiple_of' => ':attribute értéknek :value többszörösének kell lennie',
    'not_in' => ':attribute kiválasztott érték érvénytelen.',
    'not_regex' => ':attribute formátum érvénytelen.',
    'numeric' => ':attribute egy szám lehet.',
    'password' => 'A jelszó helytelen.',
    'present' => ':attribute jelen kell lennie.',
    'prohibited' => ':attribute nem megengedett.',
    'prohibited_if' => ':attribute nem megengedett, ha :other jelentése :value.',
    'prohibited_unless' => ':attribute nem megengedett, kivéve, ha :other :values-ban/ben van.',
    'regex' => ':attribute formátum érvénytelen.',
    'relatable' => ':attribute megeshet, hogy nem kapcsolódik ehhez az erőforráshoz.',
    'required' => ':attribute kötelező.',
    'required_if' => ':attribute-ra/re akkor van szükség, ha :other :value.',
    'required_unless' => ':attribute-ra/re csak akkor van szükség, ha :other :values-ban/ben van.',
    'required_with' => ':attribute akkor szükséges, ha :values van jelen.',
    'required_with_all' => ':attribute akkor szükséges, ha :values van jelen.',
    'required_without' => ':attribute akkor szükséges, ha :values nincs jelen.',
    'required_without_all' => ':attribute-ra/re akkor van szükség, ha egyik :values sincs jelen.',
    'same' => ':attribute értékének meg kell egyeznie :other értékkével.',
    'size' => [
        'array' => ':attribute pontosan :min értéket tartalmazhat.',
        'file' => ':attribute csatolmány mérete pontosan :min kilobájt lehet.',
        'numeric' => ':attribute értéke pontosan :min lehet.',
        'string' => ':attribute hossza pontosan :min karater lehet.',
    ],
    'starts_with' => ':attribute az alábbiak egyikével kell kezdődnie: :values.',
    'string' => ':attribute csak szöveg lehet.',
    'timezone' => ':attribute érvényes időzónának kell lennie.',
    'unique' => ':attribute már foglalt.',
    'uploaded' => ':attribute-t nem sikerült feltölteni.',
    'url' => ':attribute érvénytelen link formátumú.',
    'uuid' => ':attribute érvényes UUID-nak kell lennie.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'amount' => 'Az érték',
        'name' => 'A név',
        'notes' => 'A jegyzetek',
        'scope' => 'A cél',
        'transaction_date' => 'A tranzakció dátum',
        'transaction_type_id' => 'A tranzakció típus',
        'wallet_id' => 'A tárca',
        'to_wallet_id' => 'A cél tárca',
        'from_wallet_id' => 'A forrás tárca',
        'transfer_date' => 'Az átutalás dátuma',
    ],

];
