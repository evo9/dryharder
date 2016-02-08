<?php


use Illuminate\Console\Command;

class ParsePriceContentCommand extends Command
{


    protected $name = 'dh:prices-content';

    protected $description = 'Parse price content list from GoogleDocs Spreadsheet';

    public function fire()
    {
        $path = storage_path() . '/price.parse.excel.content.csv';

        $this->info('начало генерации контента прайс-листов' . "\n");

        $url = Config::get('agbis.prices.csv');
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($curl);

        if (!$content) {
            $this->error('не удалось скачать файл csv по ссылке ' . $url);

            return;
        }

        file_put_contents($path, $content);
        $this->info('скачанный файл сохранен на сервере по пути ' . $path);

        $fp = fopen($path, 'r');

        $result = [];
        while ($row = fgetcsv($fp, null, ',')) {

            if (empty($row[1])) {
                continue;
            }

            $this->line('обработка записи: ' . $row[1]);

            if ($row[0] == '*') {
                $result[] = [
                    'delimiter' => $row[1],
                    'lang'      => [
                        'ru' => $row[1],
                        'en' => empty($row[11]) ? $row[1] : $row[11],
                    ],
                    'hash' => $row[12]
                ];
                continue;
            }

            $result[] = [
                'title'    => $row[1],
                'lang'     => [
                    'ru' => $row[1],
                    'en' => empty($row[11]) ? $row[1] : $row[11],
                ],
                'standard' => [
                    'price' => $row[2],
                    'mod1'  => $row[3],
                    'mod2'  => $row[4],
                ],
                'business' => [
                    'price' => $row[5],
                    'mod1'  => $row[6],
                    'mod2'  => $row[7],
                ],
                'care'     => [
                    'price' => $row[8],
                    'mod1'  => $row[9],
                    'mod2'  => $row[10],
                ],
            ];

        }

        $this->info('найдено записей: ' . count($result) . "\n");

        $this->render($result, 'ru', 'standard');
        $this->render($result, 'ru', 'business');
        $this->render($result, 'ru', 'care');

        $this->render($result, 'en', 'standard');
        $this->render($result, 'en', 'business');
        $this->render($result, 'en', 'care');

        $this->info('генерация контента для прайс-листов завершена' . "\n");

    }

    private function render($prices, $lang, $tab)
    {
        $path = app_path() . '/../static/build/templates/prices/' . $tab . '.' . $lang . '.html';
        $this->line('генерация статического контента: ' . $path);

        $table = '<table>' . View::make('cmd::prices.thead-' . $tab . '-' . $lang)->render() . '<tbody>{content}</tbody></table>';
        $content = '';

        foreach ($prices as $item) {

            if (!empty($item['delimiter'])) {
                $content .= View::make('cmd::prices.delimiter', [
                    'title' => $item['lang'][$lang],
                    'hash' => $tab == 'standard' ? $item['hash'] : ''
                ])->render();
                continue;
            }

            if (!empty($tab)) {
                $content .= View::make('cmd::prices.row', [
                    'title' => $item['lang'][$lang],
                    'price' => $item[$tab]['price'],
                    'mod1'  => $item[$tab]['mod1'],
                    'mod2'  => $item[$tab]['mod2'],
                ])->render();
                continue;
            }

        }
        if ($tab == 'standard') {
            $content .= View::make('cmd::prices.bottom');
        }

        $table = str_replace('{content}', $content, $table);
        file_put_contents($path, $table);

        $this->line('завершено' . "\n");

    }


}