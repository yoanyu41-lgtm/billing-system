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

    'accepted' => 'វាល :attribute ត្រូវតែទទួលយក។',
    'accepted_if' => 'វាល :attribute ត្រូវតែទទួលយកនៅពេលដែល :other គឺ :value។',
    'active_url' => 'វាល :attribute មិនមែនជា URL ដែលមានសុពលភាពទេ។',
    'after' => 'វាល :attribute ត្រូវតែជាថ្ងៃបន្ទាប់ពី :date។',
    'after_or_equal' => 'វាល :attribute ត្រូវតែជាថ្ងៃបន្ទាប់ពី ឬស្មើនឹង :date។',
    'alpha' => 'វាល :attribute ត្រូវតែមានតែអក្សរតែប៉ុណ្ណោះ។',
    'alpha_dash' => 'វាល :attribute ត្រូវតែមានតែអក្សរ លេខ សញ្ញាដក និងសញ្ញាគូសក្រោមតែប៉ុណ្ណោះ។',
    'alpha_num' => 'វាល :attribute ត្រូវតែមានតែអក្សរ និងលេខតែប៉ុណ្ណោះ។',
    'array' => 'វាល :attribute ត្រូវតែជាអារេ (Array)។',
    'before' => 'វាល :attribute ត្រូវតែជាថ្ងៃមុន :date។',
    'before_or_equal' => 'វាល :attribute ត្រូវតែជាថ្ងៃមុន ឬស្មើនឹង :date។',
    'between' => [
        'array' => 'វាល :attribute ត្រូវតែមានចន្លោះពី :min ទៅ :max ធាតុ។',
        'file' => 'វាល :attribute ត្រូវតែមានទំហំចន្លោះពី :min ទៅ :max គីឡូបៃ (KB)។',
        'numeric' => 'វាល :attribute ត្រូវតែស្ថិតនៅចន្លោះពី :min ទៅ :max។',
        'string' => 'វាល :attribute ត្រូវតែមានចន្លោះពី :min ទៅ :max តួអក្សរ។',
    ],
    'boolean' => 'វាល :attribute ត្រូវតែជាពិត (true) ឬមិនពិត (false)។',
    'confirmed' => 'ការបញ្ជាក់វាល :attribute មិនត្រូវគ្នាទេ។',
    'current_password' => 'ពាក្យសម្ងាត់មិនត្រឹមត្រូវទេ។',
    'date' => 'វាល :attribute មិនមែនជាថ្ងៃខែត្រឹមត្រូវទេ។',
    'date_equals' => 'វាល :attribute ត្រូវតែជាថ្ងៃខែស្មើនឹង :date។',
    'date_format' => 'វាល :attribute មិនត្រូវគ្នានឹងទម្រង់ :format ទេ។',
    'declined' => 'វាល :attribute ត្រូវតែបដិសេធ។',
    'declined_if' => 'វាល :attribute ត្រូវតែបដិសេធនៅពេលដែល :other គឺ :value។',
    'different' => 'វាល :attribute និង :other ត្រូវតែខុសគ្នា។',
    'digits' => 'វាល :attribute ត្រូវតែមាន :digits ខ្ទង់។',
    'digits_between' => 'វាល :attribute ត្រូវតែមានចន្លោះពី :min ទៅ :max ខ្ទង់។',
    'dimensions' => 'វាល :attribute មានវិមាត្ររូបភាពមិនត្រឹមត្រូវទេ។',
    'distinct' => 'វាល :attribute មានតម្លៃស្ទួនគ្នា។',
    'doesnt_end_with' => 'វាល :attribute មិនត្រូវបញ្ចប់ដោយពាក្យដូចខាងក្រោម៖ :values។',
    'doesnt_start_with' => 'វាល :attribute មិនត្រូវចាប់ផ្ដើមដោយពាក្យដូចខាងក្រោម៖ :values។',
    'email' => 'វាល :attribute ត្រូវតែជាអាសយដ្ឋានអ៊ីមែលដែលមានសុពលភាព។',
    'ends_with' => 'វាល :attribute ត្រូវតែបញ្ចប់ដោយពាក្យដូចខាងក្រោម៖ :values។',
    'enum' => 'តម្លៃដែលបានជ្រើសរើសសម្រាប់ :attribute មិនត្រឹមត្រូវទេ។',
    'exists' => 'តម្លៃដែលបានជ្រើសរើសសម្រាប់ :attribute មិនត្រឹមត្រូវទេ។',
    'extensions' => 'វាល :attribute ត្រូវតែមានផ្នែកបន្ថែមណាមួយដូចខាងក្រោម៖ :values។',
    'file' => 'វាល :attribute ត្រូវតែជាឯកសារ។',
    'filled' => 'វាល :attribute ត្រូវតែមានតម្លៃ។',
    'gt' => [
        'array' => 'វាល :attribute ត្រូវតែមានច្រើនជាង :value ធាតុ។',
        'file' => 'វាល :attribute ត្រូវតែធំជាង :value គីឡូបៃ (KB)។',
        'numeric' => 'វាល :attribute ត្រូវតែធំជាង :value។',
        'string' => 'វាល :attribute ត្រូវតែមានច្រើនជាង :value តួអក្សរ។',
    ],
    'gte' => [
        'array' => 'វាល :attribute ត្រូវតែមានចាប់ពី :value ធាតុឡើងទៅ។',
        'file' => 'វាល :attribute ត្រូវតែធំជាង ឬស្មើ :value គីឡូបៃ (KB)។',
        'numeric' => 'វាល :attribute ត្រូវតែធំជាង ឬស្មើ :value។',
        'string' => 'វាល :attribute ត្រូវតែមានចាប់ពី :value តួអក្សរឡើងទៅ។',
    ],
    'image' => 'វាល :attribute ត្រូវតែជារូបភាព។',
    'in' => 'តម្លៃដែលបានជ្រើសរើសសម្រាប់ :attribute មិនត្រឹមត្រូវទេ។',
    'in_array' => 'វាល :attribute មិនមាននៅក្នុង :other ទេ។',
    'integer' => 'វាល :attribute ត្រូវតែជាចំនួនគត់។',
    'ip' => 'វាល :attribute ត្រូវតែជាអាសយដ្ឋាន IP ដែលមានសុពលភាព។',
    'ipv4' => 'វាល :attribute ត្រូវតែជាអាសយដ្ឋាន IPv4 ដែលមានសុពលភាព។',
    'ipv6' => 'វាល :attribute ត្រូវតែជាអាសយដ្ឋាន IPv6 ដែលមានសុពលភាព។',
    'json' => 'វាល :attribute ត្រូវតែជាខ្សែអក្សរ JSON ដែលមានសុពលភាព។',
    'lowercase' => 'វាល :attribute ត្រូវតែជាអក្សរតូចទាំងអស់។',
    'lt' => [
        'array' => 'វាល :attribute ត្រូវតែមានតិចជាង :value ធាតុ។',
        'file' => 'វាល :attribute ត្រូវតែតូចជាង :value គីឡូបៃ (KB)។',
        'numeric' => 'វាល :attribute ត្រូវតែតូចជាង :value។',
        'string' => 'វាល :attribute ត្រូវតែមានតិចជាង :value តួអក្សរ។',
    ],
    'lte' => [
        'array' => 'វាល :attribute ត្រូវតែមានមិនលើសពី :value ធាតុ។',
        'file' => 'វាល :attribute ត្រូវតែតូចជាង ឬស្មើ :value គីឡូបៃ (KB)។',
        'numeric' => 'វាល :attribute ត្រូវតែតូចជាង ឬស្មើ :value។',
        'string' => 'វាល :attribute ត្រូវតែមានមិនលើសពី :value តួអក្សរ។',
    ],
    'mac_address' => 'វាល :attribute ត្រូវតែជាអាសយដ្ឋាន MAC ដែលមានសុពលភាព។',
    'max' => [
        'array' => 'វាល :attribute មិនអាចមានច្រើនជាង :max ធាតុឡើយ។',
        'file' => 'វាល :attribute មិនអាចធំជាង :max គីឡូបៃ (KB) ឡើយ។',
        'numeric' => 'វាល :attribute មិនអាចធំជាង :max ឡើយ។',
        'string' => 'វាល :attribute មិនអាចមានច្រើនជាង :max តួអក្សរឡើយ។',
    ],
    'max_digits' => 'វាល :attribute មិនត្រូវមានលើសពី :max ខ្ទង់ឡើយ។',
    'mimes' => 'វាល :attribute ត្រូវតែជាប្រភេទឯកសារ៖ :values។',
    'mimetypes' => 'វាល :attribute ត្រូវតែជាប្រភេទឯកសារ៖ :values។',
    'min' => [
        'array' => 'វាល :attribute ត្រូវតែមានយ៉ាងហោចណាស់ :min ធាតុ។',
        'file' => 'វាល :attribute ត្រូវតែមានយ៉ាងហោចណាស់ :min គីឡូបៃ (KB)។',
        'numeric' => 'វាល :attribute ត្រូវតែមានយ៉ាងហោចណាស់ :min។',
        'string' => 'វាល :attribute ត្រូវតែមានយ៉ាងហោចណាស់ :min តួអក្សរ។',
    ],
    'min_digits' => 'វាល :attribute ត្រូវតែមានយ៉ាងហោចណាស់ :min ខ្ទង់។',
    'missing' => 'វាល :attribute ត្រូវតែបាត់បង់/មិនមាន។',
    'missing_if' => 'វាល :attribute ត្រូវតែបាត់បង់/មិនមាន នៅពេលដែល :other គឺ :value។',
    'missing_unless' => 'វាល :attribute ត្រូវតែបាត់បង់/មិនមាន លើកលែងតែ :other គឺ :value។',
    'missing_with' => 'វាល :attribute ត្រូវតែបាត់បង់/មិនមាន នៅពេលដែលមាន :values។',
    'missing_with_all' => 'វាល :attribute ត្រូវតែបាត់បង់/មិនមាន នៅពេលដែលមាន :values។',
    'multiple_of' => 'វាល :attribute ត្រូវតែជាចំនួនគុណនឹង :value។',
    'not_in' => 'តម្លៃដែលបានជ្រើសរើសសម្រាប់ :attribute មិនត្រឹមត្រូវទេ។',
    'not_regex' => 'ទម្រង់នៃវាល :attribute មិនត្រឹមត្រូវទេ។',
    'numeric' => 'វាល :attribute ត្រូវតែជាលេខ។',
    'password' => [
        'letters' => 'វាល :attribute ត្រូវតែមានយ៉ាងហោចណាស់មួយតួអក្សរ។',
        'mixed' => 'វាល :attribute ត្រូវតែមានយ៉ាងហោចណាស់អក្សរធំមួយ និងអក្សរតូចមួយ។',
        'numbers' => 'វាល :attribute ត្រូវតែមានយ៉ាងហោចណាស់លេខមួយ។',
        'symbols' => 'វាល :attribute ត្រូវតែមានយ៉ាងហោចណាស់និមិត្តសញ្ញាមួយ។',
        'uncompromised' => 'វាល :attribute ដែលបានបញ្ចូលគឺស្ថិតក្នុងបញ្ជីលេចធ្លាយទិន្នន័យ។ សូមជ្រើសរើស :attribute ផ្សេង។',
    ],
    'present' => 'វាល :attribute ត្រូវតែមានវត្តមាន។',
    'present_if' => 'វាល :attribute ត្រូវតែមានវត្តមាននៅពេលដែល :other គឺ :value។',
    'present_unless' => 'វាល :attribute ត្រូវតែមានវត្តមាន លើកលែងតែ :other គឺ :value។',
    'present_with' => 'វាល :attribute ត្រូវតែមានវត្តមាននៅពេលដែលមាន :values។',
    'present_with_all' => 'វាល :attribute ត្រូវតែមានវត្តមាននៅពេលដែលមាន :values ទាំងអស់។',
    'prohibited' => 'វាល :attribute ត្រូវបានហាមឃាត់។',
    'prohibited_if' => 'វាល :attribute ត្រូវបានហាមឃាត់នៅពេលដែល :other គឺ :value។',
    'prohibited_unless' => 'វាល :attribute ត្រូវបានហាមឃាត់ លើកលែងតែ :other ស្ថិតនៅក្នុង :values។',
    'prohibits' => 'វាល :attribute ហាមឃាត់មិនឱ្យមានវត្តមាន :other ឡើយ។',
    'regex' => 'ទម្រង់នៃវាល :attribute មិនត្រឹមត្រូវទេ។',
    'required' => 'វាល :attribute ត្រូវតែបំពេញជាដាច់ខាត។',
    'required_if' => 'វាល :attribute ត្រូវតែបំពេញនៅពេលដែល :other គឺ :value។',
    'required_if_accepted' => 'វាល :attribute ត្រូវតែបំពេញនៅពេលដែល :other ត្រូវបានទទួលយក។',
    'required_unless' => 'វាល :attribute ត្រូវតែបំពេញ លើកលែងតែ :other ស្ថិតនៅក្នុង :values។',
    'required_with' => 'វាល :attribute ត្រូវតែបំពេញនៅពេលដែលមាន :values។',
    'required_with_all' => 'វាល :attribute ត្រូវតែបំពេញនៅពេលដែលមាន :values ទាំងអស់។',
    'required_without' => 'វាល :attribute ត្រូវតែបំពេញនៅពេលដែលមិនមាន :values។',
    'required_without_all' => 'វាល :attribute ត្រូវតែបំពេញនៅពេលដែលមិនមាន :values ណាមួយឡើយ។',
    'same' => 'វាល :attribute និង :other ត្រូវតែដូចគ្នា។',
    'size' => [
        'array' => 'វាល :attribute ត្រូវតែមាន :size ធាតុ។',
        'file' => 'វាល :attribute ត្រូវតែមានទំហំ :size គីឡូបៃ (KB)។',
        'numeric' => 'វាល :attribute ត្រូវតែមានទំហំ :size។',
        'string' => 'វាល :attribute ត្រូវតែមានប្រវែង :size តួអក្សរ។',
    ],
    'starts_with' => 'វាល :attribute ត្រូវតែចាប់ផ្ដើមដោយពាក្យដូចខាងក្រោម៖ :values។',
    'string' => 'វាល :attribute ត្រូវតែជាខ្សែអក្សរ។',
    'timezone' => 'វាល :attribute ត្រូវតែជាតំបន់ម៉ោងដែលមានសុពលភាព។',
    'unique' => 'វាល :attribute នេះមានរួចហើយ។',
    'uploaded' => 'ការបញ្ចូលវាល :attribute បានបរាជ័យ។',
    'uppercase' => 'វាល :attribute ត្រូវតែជាអក្សរធំទាំងអស់។',
    'url' => 'វាល :attribute ត្រូវតែជា URL ដែលមានសុពលភាព។',
    'uuid' => 'វាល :attribute ត្រូវតែជា UUID ដែលមានសុពលភាព។',

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
        'email' => 'អាសយដ្ឋានអ៊ីមែល',
        'password' => 'ពាក្យសម្ងាត់',
        'password_confirmation' => 'ការបញ្ជាក់ពាក្យសម្ងាត់',
        'name' => 'ឈ្មោះ',
        'username' => 'ឈ្មោះអ្នកប្រើប្រាស់',
        'phone' => 'លេខទូរស័ព្ទ',
        'address' => 'អាសយដ្ឋាន',
        'role' => 'តួនាទី',
    ],

];
