<?php

namespace App\Controller;

use App\Model\UserManager;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    public const FILTERS = [
        'string' => FILTER_UNSAFE_RAW,
        'string[]' => [
            'filter' => FILTER_UNSAFE_RAW,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'email' => FILTER_SANITIZE_EMAIL,
        'int' => [
            'filter' => FILTER_SANITIZE_NUMBER_INT,
            'flags' => FILTER_REQUIRE_SCALAR
        ],
        'int[]' => [
            'filter' => FILTER_SANITIZE_NUMBER_INT,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'float' => [
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_FLAG_ALLOW_FRACTION
        ],
        'float[]' => [
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'date' => FILTER_UNSAFE_RAW,
        'password' => FILTER_UNSAFE_RAW
    ];
    public const VALIDATERS = [
        'string' => 'validateString',
        'string[]' => [
            'filter' => FILTER_UNSAFE_RAW,
            'flag' => FILTER_REQUIRE_ARRAY
        ],
        'email' => 'validateEmail',
        'int' => [
            'filter' => FILTER_VALIDATE_INT,
            'flag' => FILTER_REQUIRE_SCALAR
        ],
        'int[]' => [
            'filter' => FILTER_VALIDATE_INT,
            'flag' => FILTER_REQUIRE_ARRAY
            ],
        'float' => [
            'filter' => FILTER_VALIDATE_FLOAT,
            'flags' => FILTER_FLAG_ALLOW_FRACTION
        ],
        'float[]' => [
            'filter' => FILTER_VALIDATE_FLOAT,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'date' => 'validateDate',
        'password' => 'validatePassword'
    ];
    public const FIELDS = [
        'first_name' => 'string',
        'last_name' => 'string',
        'username' => 'string',
        'email' => 'email',
        'birthday' => 'date',
        'password' => 'string'
    ];

    public const FIELDS_LOGIN = [
        'username' => 'string',
        'password' => 'string'
    ];

    public const FIELDS_REGISTER = [
        'new-username' => 'string',
        'new-email' => 'email',
        'new-password' => 'string'
    ];

    public const FIELDS_EDIT = [
        'firstName' => 'string',
        'lastName' => 'string',
        'username' => 'string',
        'email' => 'email',
        'birthday' => 'date',
    ];

    protected Environment $twig;
    protected array | false $user;
    protected array $errorsLogin;
    protected array $errorsRegister;
    protected int $login;
    protected int $register;


    public function __construct()
    {
        $loader = new FilesystemLoader(APP_VIEW_PATH);
        $this->twig = new Environment(
            $loader,
            [
                'cache' => false,
                'debug' => true,
            ]
        );
        $this->twig->addExtension(new DebugExtension());
        $userManager = new UserManager();
        $this->user = isset($_SESSION['user_id']) ? $userManager->selectOneById($_SESSION['user_id']) : false;
        $this->twig->addGlobal('user', $this->user);
        if (!empty($_SESSION['errorsLogin'])) {
            $this->errorsLogin = $_SESSION['errorsLogin'];
            $this->twig->addGlobal('errorsLogin', $this->errorsLogin);
            $this->login = 1;
            $this->twig->addGlobal('login', $this->login);
        }
        if (!empty($_SESSION['errorsRegister'])) {
            $this->errorsRegister = $_SESSION['errorsRegister'];
            $this->twig->addGlobal('errorsRegister', $this->errorsRegister);
            $this->register = 2;
            $this->twig->addGlobal('register', $this->register);
        }
    }

    public function arrayTrim(array $inputs): array
    {
        return array_map(function ($input) {
            if (is_string($input)) {
                return trim($input);
            } elseif (is_array($input)) {
                return $this->arrayTrim($input);
            } else {
                return $input;
            }
        }, $inputs);
    }

    public function sanitizeData(
        array $inputs,
        array $fields = [],
        int $defaultFilter = FILTER_UNSAFE_RAW,
        array $filters = self::FILTERS
    ): array {
        if ($fields) {
            $options = array_map(fn($field) => $filters[$field], $fields);
            $data = filter_var_array($inputs, $options);
            $data = $this->arrayTrim($data);
        } else {
            $data = filter_var_array($inputs, $defaultFilter);
        }

        return $data;
    }
    public function validateData(
        array $inputs,
        array $fields = [],
        array $validaters = self::VALIDATERS
    ): array | bool {
        if ($fields) {
            $errors = [];
            foreach ($inputs as $keyInputs => $input) {
                foreach ($fields as $keyFields => $field) {
                    if ($keyInputs === $keyFields) {
                        foreach ($validaters as $keyValidaters => $validater) {
                            if ($field === $keyValidaters) {
                                if (!empty($this->$validater($input))) {
                                    $errors[$keyInputs] = $this->$validater($input);
                                }
                            }
                        }
                    }
                }
            }
            return $errors;
        }
        return false;
    }

    public function validateEmail(string $email): array
    {
        $errors = [];
        if (empty($email)) {
            $errors['fatal']['emptyEmail'] = "Veuillez entrer une adresse email";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Veuillez entrer une adresse email valide";
        }
        if (strlen($email) >= 50) {
            $errors[] = "Votre adresse email est trop longue";
        }
        if (strlen($email) <= 5) {
            $errors[] = "Votre adresse email est trop courte";
        }
            return $errors;
    }

    public function validateString(string $string): array
    {
        $errors = [];
        if (empty($string)) {
            $errors['fatal']['emptyField'] = " est vide";
        } elseif (strlen($string) >= 50) {
            $errors[]['fieldLength'] = " est trop long";
        } elseif (strlen($string) <= 3) {
            $errors[]['fieldLength'] = " est trop court";
        }

        return $errors;
    }

    public function validateDate(mixed $date): array
    {
        $errors = [];
        if (empty($date)) {
            $errors['fatal']['emptyField'] = " est vide";
        }

        return $errors;
    }

    public function validatePassword(string $password): array
    {
        $errors = [];
        if (empty($password)) {
            $errors['fatal']['emptyField'] = "Le mot de passe est vide";
        } elseif (strlen($password) <= 5) {
            $errors[]['fieldLength'] = "Le mot de passe est trop court";
        }

        return $errors;
    }
}
