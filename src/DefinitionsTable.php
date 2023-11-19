<?php

declare(strict_types=1);

namespace PixelPlugin\WPContainer;

use WP_List_Table;

use function __;
use function esc_html;
use function get_option;

/**
 * @since 1.2.0
 */
final class DefinitionsTable extends WP_List_Table
{
    public function get_columns(): array
    {
        return [
            'id' => __('ID'),
            'value' => __('Value')
        ];
    }

    /**
     * @return void
     */
    public function prepare_items()
    {
        $definitions = get_option(Plugin::OPTION_DEFINITIONS, []);
        $this->items = [];

        foreach ($definitions as list($id, $value)) {
            $this->items[] = ['id' => $id, 'value' => $value];
        }

        $this->_column_headers = [$this->get_columns(), [], []];
    }

    /**
     * @param array $item
     * @param string $column_name
     * @return string
     */
    protected function column_default($item, $column_name): string
    {
        return esc_html((string) $item[$column_name]);
    }

    protected function get_default_primary_column_name(): string
    {
        return 'id';
    }
}
