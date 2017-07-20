<?php

namespace Cheppers\GatherContent\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GcBaseTestCase extends TestCase
{
    /**
     * @var array
     */
    protected $gcClientOptions = [
      'baseUri' => 'https://api.example.com',
      'email' => 'a@b.com',
      'apiKey' => 'a-b-c-d',
    ];

    protected static $uniqueNumber = 1;

    protected static function getUniqueInt(): int
    {
        return static::$uniqueNumber++;
    }

    protected static function getUniqueFloat(): float
    {
        return static::$uniqueNumber++ + (rand(1, 9) / 10);
    }

    protected static function getUniqueString(string $prefix): string
    {
        return "$prefix-" . static::$uniqueNumber++;
    }

    protected static function getUniqueEmail(string $prefix): string
    {
        return sprintf(
            '%s@%s.com',
            static::getUniqueString($prefix),
            static::getUniqueString($prefix)
        );
    }

    protected static function getUniqueDate(): string
    {
        return date('Y-m-d H:i:s', rand(0, time()));
    }

    protected static function getUniqueResponseAnnouncement(): array
    {
        return [
            'id' => static::getUniqueInt(),
            'name' => static::getUniqueString('name'),
            'acknowledged' => static::getUniqueString('acknowledged'),
        ];
    }

    protected static function getUniqueResponseFile():array
    {
        return [
            'id' => static::getUniqueInt(),
            'user_id' => static::getUniqueInt(),
            'item_id' => static::getUniqueInt(),
            'field' => 'field',
            'type' => 'type',
            'url' => static::getUniqueString('http://'),
            'filename' => static::getUniqueString('fileName'),
            'size' => static::getUniqueInt(),
            'created_at' => static::getUniqueDate(),
            'updated_at' => static::getUniqueDate(),
        ];
    }

    protected static function getUniqueResponseUser(): array
    {
        return [
            'email' => 'email',
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'language' => 'language',
            'gender' => 'gender',
            'avatar' => 'avatar',
            'announcements' => [
                static::getUniqueResponseAnnouncement(),
                static::getUniqueResponseAnnouncement(),
                static::getUniqueResponseAnnouncement(),
            ],
        ];
    }

    protected static function getUniqueResponseAccount(): array
    {
        return [
            'id' => static::getUniqueInt(),
            'name' => static::getUniqueString('name'),
            'slug' => static::getUniqueString('slug'),
            'timezone' => static::getUniqueString('timezone'),
        ];
    }

    protected static function getUniqueResponseProject(): array
    {
        $allowedTags = [
            'a' => ['class' => '*'],
        ];

        return [
            'id' => static::getUniqueInt(),
            'name' => static::getUniqueString('name'),
            'type' => static::getUniqueString('type'),
            'example' => true,
            'account_id' => static::getUniqueInt(),
            'active' => true,
            'text_direction' => static::getUniqueString('text_direction'),
            'allowed_tags' => json_encode($allowedTags, JSON_PRETTY_PRINT),
            'created_at' => static::getUniqueInt(),
            'updated_at' => static::getUniqueInt(),
            'overdue' => true,
            'statuses' => [
                'data' => [
                    static::getUniqueResponseStatus(),
                    static::getUniqueResponseStatus(),
                    static::getUniqueResponseStatus(),
                ],
            ],
        ];
    }

    protected static function getUniqueResponseDate(): array
    {
        return [
            'date' => static::getUniqueDate(),
            'timezone_type' => static::getUniqueInt(),
            'timezone' => static::getUniqueString('timezone'),
        ];
    }

    protected static function getUniqueResponseStatus(): array
    {
        return [
            'id' => static::getUniqueInt(),
            'is_default' => false,
            'position' => static::getUniqueString('position'),
            'color' => static::getUniqueString('color'),
            'name' => static::getUniqueString('name'),
            'description' => static::getUniqueString('description'),
            'can_edit' => true,
        ];
    }

    protected static function getUniqueResponseTab(array $elements): array
    {
        $tab = [
            'name' => static::getUniqueString('tab'),
            'label' => static::getUniqueString('label'),
            'hidden' => false,
            'elements' => [],
        ];

        foreach ($elements as $elementType) {
            switch ($elementType) {
                case 'text':
                    $tab['elements'][] = static::getUniqueResponseElementText();
                    break;

                case 'files':
                    $tab['elements'][] = static::getUniqueResponseElementFiles();
                    break;

                case 'section':
                    $tab['elements'][] = static::getUniqueResponseElementSection();
                    break;

                case 'choice_radio':
                    $tab['elements'][] = static::getUniqueResponseElementChoiceRadio();
                    break;

                case 'choice_checkbox':
                    $tab['elements'][] = static::getUniqueResponseElementChoiceCheckbox();
                    break;
            }
        }

        return $tab;
    }

    protected static function getUniqueResponseTemplateTab(array $elements): array
    {
        $tab = [
            'name' => static::getUniqueString('tab'),
            'label' => static::getUniqueString('label'),
            'hidden' => false,
            'elements' => [],
        ];

        foreach ($elements as $elementType) {
            switch ($elementType) {
                case 'text':
                    $tab['elements'][] = static::getUniqueResponseElementText();
                    break;

                case 'files':
                    $tab['elements'][] = static::getUniqueResponseElementTemplateFiles();
                    break;

                case 'section':
                    $tab['elements'][] = static::getUniqueResponseElementSection();
                    break;

                case 'choice_radio':
                    $tab['elements'][] = static::getUniqueResponseElementChoiceRadio();
                    break;

                case 'choice_checkbox':
                    $tab['elements'][] = static::getUniqueResponseElementChoiceCheckbox();
                    break;
            }
        }

        return $tab;
    }

    protected static function getUniqueResponseElementTemplateFiles(): array
    {
        return [
            'name' => static::getUniqueString('el'),
            'type' => 'files',
            'label' => static::getUniqueString('label'),
            'required' => false,
            'microcopy' => '',
        ];
    }

    protected static function getUniqueResponseElementText(): array
    {
        return [
            'name' => static::getUniqueString('el'),
            'type' => 'text',
            'label' => static::getUniqueString('label'),
            'required' => false,
            'microcopy' => '',
            'limit_type' => static::getUniqueString('limit_type'),
            'limit' => static::getUniqueInt(),
            'plain_text' => true,
            'value' => static::getUniqueString('value'),
        ];
    }

    protected static function getUniqueResponseElementFiles(): array
    {
        return [
            'name' => static::getUniqueString('el'),
            'type' => 'files',
            'label' => static::getUniqueString('label'),
            'required' => false,
            'microcopy' => '',
            'user_id' => static::getUniqueInt(),
            'item_id' => static::getUniqueInt(),
            'field' => static::getUniqueString('el'),
            'url' => static::getUniqueString('https://'),
            'filename' => static::getUniqueString('myFileName'),
            'size' => static::getUniqueInt(),
            'created_at' => static::getUniqueDate(),
            'updated_at' => static::getUniqueDate(),
        ];
    }

    protected static function getUniqueResponseElementSection(): array
    {
        return [
            'name' => static::getUniqueString('el'),
            'type' => 'section',
            'title' => static::getUniqueString('title'),
            'subtitle' => static::getUniqueString('subtitle'),
        ];
    }

    protected static function getUniqueResponseElementChoiceRadio(): array
    {
        return [
            'name' => static::getUniqueString('el'),
            'type' => 'choice_radio',
            'label' => static::getUniqueString('label'),
            'required' => false,
            'microcopy' => '',
            'options' => static::getUniqueResponseElementChoiceOptions(false),
            'other_option' => false,
        ];
    }

    protected static function getUniqueResponseElementChoiceCheckbox(): array
    {
        return [
            'name' => static::getUniqueString('el'),
            'type' => 'choice_checkbox',
            'label' => static::getUniqueString('label'),
            'required' => false,
            'microcopy' => '',
            'options' => static::getUniqueResponseElementChoiceOptions(true),
        ];
    }

    protected static function getUniqueResponseElementChoiceOptions(bool $multiple): array
    {
        $amount = rand(1, 5);
        $keys = range(1, $amount);
        shuffle($keys);
        $selected = array_slice($keys, 0, rand(0, ($multiple ? $amount : 1)));
        $options = [];
        for ($i = 0; $i < $amount; $i++) {
            $options[] = [
                'name' => static::getUniqueString('name'),
                'label' => static::getUniqueString('label'),
                'selected' => in_array($i, $selected),
            ];
        }

        return $options;
    }

    protected static function getUniqueResponseItem(array $tabs): array
    {
        $item = [
            'id' => static::getUniqueInt(),
            'project_id' => static::getUniqueInt(),
            'parent_id' => static::getUniqueInt(),
            'template_id' => static::getUniqueInt(),
            'custom_state_id' => static::getUniqueInt(),
            'position' => static::getUniqueString('position'),
            'name' => static::getUniqueString('name'),
            'config' => [],
            'notes' => static::getUniqueString('notes'),
            'type' => 'item',
            'overdue' => false,
            'archived_by' => static::getUniqueInt(),
            'archived_at' => static::getUniqueInt(),
            'created_at' => static::getUniqueResponseDate(),
            'updated_at' => static::getUniqueResponseDate(),
            'status' => [
                'data' => static::getUniqueResponseStatus(),
            ],
            'due_dates' => [
                'data' => [
                    static::getUniqueResponseDate(),
                    static::getUniqueResponseDate(),
                    static::getUniqueResponseDate(),
                ],
            ],
        ];

        foreach ($tabs as $elements) {
            $item['config'][] = static::getUniqueResponseTab($elements);
        }

        return $item;
    }

    protected static function getUniqueResponseTemplate(array $tabs): array
    {
        $template = [
            'id' => static::getUniqueInt(),
            'project_id' => static::getUniqueInt(),
            'created_by' => static::getUniqueInt(),
            'updated_by' => static::getUniqueInt(),
            'name' => static::getUniqueString('name'),
            'description' => static::getUniqueString('description'),
            'config' => [],
            'used_at' => null,
            'created_at' => static::getUniqueResponseDate(),
            'updated_at' => static::getUniqueResponseDate(),
            'usage' => [
                'item_count' => static::getUniqueInt(),
            ],
        ];

        foreach ($tabs as $elements) {
            $template['config'][] = static::getUniqueResponseTemplateTab($elements);
        }

        return $template;
    }

    protected static function reKeyArray(array $array, string $key): array
    {
        $items = [];
        foreach ($array as $item) {
            $items[$item[$key]] = $item;
        }

        return $items;
    }

    protected static function basicFailCases($data = null): array
    {
        return [
            'unauthorized' => [
                [
                    'class' => \Exception::class,
                    'code' => 401,
                    'msg' => '401 Unauthorized',
                ],
                [
                    'code' => 401,
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => '401 Unauthorized',
                ],
                42,
                (isset($data['id']) ? $data['id'] : (isset($data['name']) ? $data['name'] : null)),
                (isset($data['type']) ? $data['type'] : null)
            ],
            'internal-error' => [
                [
                    'class' => \Exception::class,
                    'code' => 500,
                    'msg' => '{"error":"unknown error"}',
                ],
                [
                    'code' => 500,
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => [
                        'error' => 'unknown error'
                    ],
                ],
                42,
                (isset($data['id']) ? $data['id'] : (isset($data['name']) ? $data['name'] : null)),
                (isset($data['type']) ? $data['type'] : null)
            ],
        ];
    }

    protected static function basicFailCasesGet($data = null): array
    {
        $cases = self::basicFailCases($data);
        $cases['header-error'] = [
            [
                'class' => \Exception::class,
                'code' => 1,
                'msg' => 'Unexpected Content-Type: \'text/css\'',
            ],
            [
                'code' => 200,
                'headers' => ['Content-Type' => 'text/css'],
                'body' => [],
            ],
            42,
            (isset($data['id']) ? $data['id'] : (isset($data['name']) ? $data['name'] : null)),
            (isset($data['type']) ? $data['type'] : null)
        ];

        return $cases;
    }

    protected static function basicFailCasesPost($data = null): array
    {
        $cases = self::basicFailCases($data);
        $cases['header-error'] = [
            [
                'class' => \Exception::class,
                'code' => 1,
                'msg' => 'Unexpected answer',
            ],
            [
                'code' => 200,
                'headers' => ['Content-Type' => 'text/css'],
                'body' => [],
            ],
            42,
            (isset($data['id']) ? $data['id'] : (isset($data['name']) ? $data['name'] : null)),
            (isset($data['type']) ? $data['type'] : null)
        ];

        return $cases;
    }

    public function getBasicHttpClientTester(array $requests)
    {
        $requests[] = new RequestException(
            'Error Communicating with Server',
            new Request('GET', 'unexpected_request')
        );
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler($requests);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        return [
            'client' => $client,
            'container' => &$container,
            'history' => $history,
            'handlerStack' => $handlerStack,
            'mock' => $mock,
        ];
    }
}
