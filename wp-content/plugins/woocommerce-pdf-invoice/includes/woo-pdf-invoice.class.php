<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WooPdfInvoice')) {

/**
 * PDF generation class
 *
 * @class WooPDFInvoice
 * @package WooCommerce_PDF_Invoice
 * @author RightPress
 */
class WooPdfInvoice extends TCPDF
{

    private $invoiceOptions;
    private $orderData;
    private $invoiceInfo;
    private $current_page;
    private $page_count;
    private $wc_countries;

    /**
     * Class constructor
     *
     * @access public
     * @param array $data
     * @param string $orientation
     * @param string $unit
     * @param string $format
     * @return void
     */
    function __construct($data, $orientation, $unit, $format)
    {

        parent::__construct($orientation, $unit, $format, true, 'UTF-8', false);

        // Development settings
        $this->showBorders = 0;

        // Parse order data
        $this->invoiceOptions = $data['options'];
        $this->orderData = $data['order'];
        $this->invoiceInfo = $data['info'];
        $this->invoiceType = $data['type'];

        $this->order_tax = null;
        $this->tax_rates = null;
        $this->totals = null;

        // Check if different tax rates are used for different items and if so - get total for each of them
        $this->multiple_tax_rates = $this->maybe_get_multiple_tax_totals();

        // Convert HTML entities to regular characters
        $invoice_options = $this->invoiceOptions;
        $this->invoiceOptions = array();
        foreach ($invoice_options as $key => $value) {
            $this->invoiceOptions[$key] = htmlspecialchars_decode($value);
        }

        // Initial setup
        $this->footer_height = $this->get_height('footer');
        $this->max_first_page_height = 800 - $this->footer_height;
        $this->max_page_height = 800 - $this->footer_height;
        $this->current_page = 1;
        $this->page_count = 1;

        // Set document meta-information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor($this->invoiceOptions['woo_pdf_seller_name']);
        $document_title = ($this->invoiceType == 'invoice' ?
                $this->invoiceOptions['woo_pdf_document_name'] . ' ' . $this->invoiceInfo['prefix'] . ' ' . $this->invoiceInfo['id'] . ' ' . $this->invoiceInfo['suffix'] :
                $this->invoiceOptions['woo_pdf_proforma_name'] . ' ' . $this->invoiceInfo['id']);
        $this->SetTitle($document_title);
        $this->SetSubject($this->invoiceOptions['woo_pdf_document_name']);
        $this->SetKeywords($this->invoiceOptions['woo_pdf_seller_name'], $this->invoiceInfo['id']);

        // Set the page margins: 36pt on all sides
        $this->SetMargins(36, 36, 36, true);
        $this->SetAutoPageBreak(true, 36);

        // Set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set some language-dependent strings
        global $l;
        $this->setLanguageArray($l);

    }

    /**
     * Create PDF invoice
     *
     * @access public
     * @return void
     */
    public function CreateInvoice()
    {
        // Add first page
        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);
        $this->AddPage();

        // Track pointer
        $y = 40;
        $r = 12;
        $s = 5;

        $y = $this->render_first_page_header($y, $r, $s);

        // Padding after header
        $y = $y + 40;

        $y = $this->render_table_header($y, $r, $s);

        // Padding after table header
        $y = $y + 4;

        $this->style();

        foreach ($this->orderData->get_items() as $item) {

            $_product = RightPress_WC_Legacy::order_item_get_product($item, $this->orderData);
            $item_meta = $this->item_meta($item);

            // Maybe prepend short product description
            if (is_object($_product)) {
                if ($this->invoiceOptions['woo_pdf_display_short_description']) {

                    // Get short description
                    $short_description = RightPress_WC_Legacy::product_get_short_description($_product);

                    if (!empty($short_description)) {
                        $post_excerpt = $this->invoiceOptions['woo_pdf_title_description'] . ': ' . strip_tags($short_description);
                        $item_meta = empty($item_meta) ? $post_excerpt : $post_excerpt . PHP_EOL . $item_meta;
                    }
                }
            }

            // Maybe prepend list of product categories
            if (is_object($_product)) {
                if ($this->invoiceOptions['woo_pdf_display_category'] && $current_item_categories = $this->get_product_category_names(RightPress_WC_Legacy::product_get_id($_product))) {
                    $current_item_categories = $this->invoiceOptions['woo_pdf_title_category'] . ': ' . join(', ', $current_item_categories);
                    $item_meta = empty($item_meta) ? $current_item_categories : $current_item_categories . PHP_EOL . $item_meta;
                }
            }

            $current_meta_height = $this->get_height('meta', $item_meta);

            // Maybe display product images
            $product_images = array();
            $current_images_height = 0;

            if (is_object($_product)) {
                if ($this->invoiceOptions['woo_pdf_display_product_thumbnails']) {
                    $product_image_ids = array_merge(array($_product->get_image_id()), $_product->get_gallery_image_ids());

                    if (is_array($product_image_ids) && !empty($product_image_ids)) {
                        $product_images = (count($product_image_ids) > 5) ? array_slice($product_image_ids, 0, 5) : $product_image_ids;
                        $current_images_height = 100;
                    }
                }
            }

            // Add new page if needed
            if (($y + $s + $r + 2 + (empty($item_meta) ? 0 : ($current_meta_height + 2)) + $current_images_height + 20) > $this->max_first_page_height) {
                $y = $this->add_page();
            }

            $row = array(
                'name' => RightPress_WC_Legacy::order_item_get_name($item),
                'price' => $this->item_price($item, $this->orderData),
                'quantity' => RightPress_WC_Legacy::order_item_get_quantity($item),
                'meta' => $item_meta,
                'images' => $product_images,
                'net' => RightPress_WC_Legacy::order_item_get_subtotal($item),
                'tax_rate' => $this->get_item_tax_rate(RightPress_WC_Legacy::order_item_get_subtotal($item), RightPress_WC_Legacy::order_item_get_subtotal_tax($item)),
                'tax' => RightPress_WC_Legacy::order_item_get_subtotal_tax($item),
                'total' => $this->row_total($item, $this->orderData),
            );

            // Check if we need to display product ID/SKU
            $item_name = $row['name'];
            if ($this->invoiceOptions['woo_pdf_display_product_id'] == 1) {
                $item_name = RightPress_WC_Legacy::order_item_get_product_id($item) . ' - ' . $item_name;
            }
            else if ($this->invoiceOptions['woo_pdf_display_product_id'] == 2 && is_object($_product)) {
                $item_sku = $_product->get_sku();
                if ($item_sku != '')
                    $item_name = $item_sku . ' - ' . $item_name;
            }

            // Maybe display more decimals to prevent issues because of rounding
            $price = $this->get_rounded_price($row['price'], $row['quantity']);

            // Different tax rates used
            if (($this->invoiceOptions['woo_pdf_display_tax_inline'] == 0 && $this->multiple_tax_rates) || $this->invoiceOptions['woo_pdf_display_tax_inline'] == 1) {

                // Name
                $this->MultiCell(200, $r, $this->decode($item_name), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 0, 'T', false);

                // Get real row height
                $true_r = $this->getY() - $y;

                // Price
                $this->MultiCell(60, $true_r, $price, $this->showBorders, 'R', 0, 1, 240, $y, true, 0, false, false, $true_r, 'T', false);

                // Quantity
                $this->MultiCell(40, $true_r, $row['quantity'], $this->showBorders, 'R', 0, 1, 300, $y, true, 0, false, false, $true_r, 'T', false);

                // Net
                $this->MultiCell(60, $true_r, $this->format_currency($row['net']), $this->showBorders, 'R', 0, 1, 340, $y, true, 0, false, false, $true_r, 'T', false);

                // Tax %
                $this->MultiCell(50, $true_r, $row['tax_rate'] . ' %', $this->showBorders, 'R', 0, 1, 400, $y, true, 0, false, false, $true_r, 'T', false);

                // Tax
                $this->MultiCell(50, $true_r, $this->format_currency($row['tax']), $this->showBorders, 'R', 0, 1, 450, $y, true, 0, false, false, $true_r, 'T', false);

                // Total
                $this->MultiCell(60, $true_r, $this->format_currency($row['total']), $this->showBorders, 'R', 0, 1, 500, $y, true, 0, false, false, $true_r, 'T', false);

                // Meta
                if (!empty($row['meta'])) {
                    $y += $true_r + 2;
                    $this->style('gray');
                    $this->MultiCell(515, 0, $this->decode($row['meta']), $this->showBorders, 'L', 0, 1, 45, $y, true, 0, false, false, 0, 'T', false);
                    $y += $current_meta_height;
                    $this->style();
                }

                // Images
                if (!empty($row['images'])) {
                    $y += empty($row['meta']) ? ($true_r + 7) : 0;

                    $i = 0;

                    foreach ($row['images'] as $attachment_id) {
                        $product_image_url = wp_get_attachment_url($attachment_id);
                        $this->Image($product_image_url, (50 + ($i * 100)), $y, 90, 90, '', '', 'T', true, 300, '', false, false, $this->showBorders, true, false, false, false, false);
                        $i++;
                    }

                    $y += $current_images_height;
                }

                $y += $s + ((!empty($row['meta']) || !empty($row['images'])) ? 0 : $true_r);
            }

            // Different tax rates not used or feature disabled
            else {

                // Name
                $this->MultiCell(280, $r, $this->decode($item_name), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 0, 'T', false);

                // Get real row height
                $true_r = $this->getY() - $y;

                // Price
                $this->MultiCell(80, $true_r, $price, $this->showBorders, 'R', 0, 1, 320, $y, true, 0, false, false, $true_r, 'T', false);

                // Quantity
                $this->MultiCell(80, $true_r, $row['quantity'], $this->showBorders, 'R', 0, 1, 400, $y, true, 0, false, false, $true_r, 'T', false);

                // Total
                $this->MultiCell(80, $true_r, $this->format_currency($row['total']), $this->showBorders, 'R', 0, 1, 480, $y, true, 0, false, false, $true_r, 'T', false);

                // Meta
                if (!empty($row['meta'])) {
                    $y += $true_r + 2;
                    $this->style('gray');
                    $this->MultiCell(515, 0, $this->decode($row['meta']), $this->showBorders, 'L', 0, 1, 45, $y, true, 0, false, false, 0, 'T', false);
                    $y += $current_meta_height;
                    $this->style();
                }

                // Images
                if (!empty($row['images'])) {
                    $y += empty($row['meta']) ? ($true_r + 7) : 0;

                    $i = 0;

                    foreach ($row['images'] as $attachment_id) {
                        $product_image_url = wp_get_attachment_url($attachment_id);
                        $this->Image($product_image_url, (50 + ($i * 100)), $y, 90, 90, '', '', 'T', true, 300, '', false, false, $this->showBorders, true, false, false, false, false);
                        $i++;
                    }

                    $y += $current_images_height;
                }

                $y += $s + ((!empty($row['meta']) || !empty($row['images'])) ? 0 : $true_r);
            }

            // Draw bottom border
            $this->Line(40, ($y-2), 560, ($y-2), array('color' => array(192, 192, 192)));
            $y += 2;
        }

        $y = $this->render_totals($y, $r, $s);

        // Reset cell padding
        $this->SetCellPaddings(2.835, 0, 2.835, 0);

        // Amount in words
        if ($this->invoiceOptions['woo_pdf_amount_in_words']) {
            $y = $this->render_left_block($y, $r, $s, 'amount_in_words');
        }

        // Render custom content blocks
        foreach (array('1', '2', '3', '4') as $n) {
            if ($this->invoiceOptions['woo_pdf_block_'.$n.'_show'] == 0 || ($this->invoiceOptions['woo_pdf_block_'.$n.'_show'] == 1 && $this->invoiceType == 'invoice') || ($this->invoiceOptions['woo_pdf_block_'.$n.'_show'] == 2 && $this->invoiceType == 'proforma')) {
                if ($this->invoiceOptions['woo_pdf_block_'.$n.'_title'] != '' || $this->invoiceOptions['woo_pdf_block_'.$n.'_content'] != '') {
                    $y = $this->render_left_block($y, $r, $s, 'block_'.$n);
                }
            }
        }

        $this->render_footer();

        if ($this->page_count > 1) {
            $this->render_page_numbers();
        }
    }

    /**
     * Determine height of element
     *
     * @access public
     * @param string $context
     * @param string $string
     * @return mixed
     */
    public function get_height($context, $string = '', $s = 0)
    {
        // Get current page
        $current_page = $this->getPage();

        // Add blank page to test height
        $this->AddPage();
        $this->SetY(0);

        // Test render string (depending on context)
        if ($context == 'footer') {
            $this->render_footer(true);
        }
        else if ($context == 'meta') {
            $this->MultiCell(515, 0, $this->decode($string), $this->showBorders, 'L', 0, 1, 45, 0, true, 0, false, false, 0, 'T', false);
        }
        else if ($context == 'block') {

            $block_strings = explode(PHP_EOL, $this->decode($string));
            $allowed_height = $this->current_page == 1 ? $this->max_first_page_height : $this->max_page_height;
            $y = 0;

            // Iterate through strings
            foreach ($block_strings as $string) {

                $this->MultiCell(520, 0, $string, $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 0, 'T', false);
                $y = $this->GetY();

                // Stop if it exceeds one page
                if (($y + (20 + 15 + $s + 20)) > $allowed_height) {
                    break;
                }
            }
        }

        // Get current test page
        $test_page = $this->getPage();

        // Get y position and remove the page
        $y = $this->getY();

        // Make sure to delete all test pages
        for ($i = $current_page + 1; $i <= $test_page; $i++) {
            $this->deletePage($i);
        }

        return $y;
    }

    /**
     * Render table header
     *
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return void
     */
    public function render_table_header($y = 240, $r = 0, $s = 0)
    {
        $this->style('b');
        $this->SetCellPadding(5);

        // Different tax rates used
        if (($this->invoiceOptions['woo_pdf_display_tax_inline'] == 0 && $this->multiple_tax_rates) || $this->invoiceOptions['woo_pdf_display_tax_inline'] == 1) {

            // Name
            $this->MultiCell(200, 20, $this->decode($this->invoiceOptions['woo_pdf_title_product']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 20, 'M', false);

            // Price
            $this->MultiCell(60, 20, $this->decode($this->invoiceOptions['woo_pdf_title_price']), $this->showBorders, 'R', 0, 1, 240, $y, true, 0, false, false, 20, 'M', false);

            // Quantity
            $this->MultiCell(40, 20, $this->decode($this->invoiceOptions['woo_pdf_title_quantity']), $this->showBorders, 'R', 0, 1, 300, $y, true, 0, false, false, 20, 'M', false);

            // Net
            $this->MultiCell(60, 20, $this->decode($this->invoiceOptions['woo_pdf_title_net']), $this->showBorders, 'R', 0, 1, 340, $y, true, 0, false, false, 20, 'M', false);

            // Tax %
            $this->MultiCell(50, 20, $this->decode($this->invoiceOptions['woo_pdf_title_tax_percent']), $this->showBorders, 'R', 0, 1, 400, $y, true, 0, false, false, 20, 'M', false);

            // Tax
            $this->MultiCell(50, 20, $this->decode($this->invoiceOptions['woo_pdf_title_tax']), $this->showBorders, 'R', 0, 1, 450, $y, true, 0, false, false, 20, 'M', false);

            // Total
            $this->MultiCell(60, 20, $this->decode($this->invoiceOptions['woo_pdf_title_line_total']), $this->showBorders, 'R', 0, 1, 500, $y, true, 0, false, false, 20, 'M', false);

        }

        // Different tax rates not used or feature disabled
        else {

            // Name
            $this->MultiCell(280, 20, $this->decode($this->invoiceOptions['woo_pdf_title_product']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 20, 'M', false);

            // Price
            $this->MultiCell(80, 20, $this->decode($this->invoiceOptions['woo_pdf_title_price']), $this->showBorders, 'R', 0, 1, 320, $y, true, 0, false, false, 20, 'M', false);

            // Quantity
            $this->MultiCell(80, 20, $this->decode($this->invoiceOptions['woo_pdf_title_quantity']), $this->showBorders, 'R', 0, 1, 400, $y, true, 0, false, false, 20, 'M', false);

            // Total
            $this->MultiCell(80, 20, $this->decode($this->invoiceOptions['woo_pdf_title_line_total']), $this->showBorders, 'R', 0, 1, 480, $y, true, 0, false, false, 20, 'M', false);

        }

        // Draw bottom border
        $this->Line(40, ($y+20), 560, ($y+20), array('color' => array(192, 192, 192)));

        return $this->getY();
    }

    /**
     * Render totals
     *
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return int
     */
    public function render_totals($y, $r, $s)
    {
        // Get totals
        if (is_null($this->totals)) {
            $this->totals = $this->get_totals();
        }

        // Calculate proposed height
        $height = 10 + 15; // adding 15 for higher than usual "totals" field
        foreach ($this->totals as $key => $value) {
            if (!is_null($value['value'])) {
                $count = is_array($value['value']) ? count($value['value']) : 1;
                $height += ($r + $s) * $count;
            }
        }

        // Add new page if needed
        $allowed_height = $this->current_page == 1 ? $this->max_first_page_height : $this->max_page_height;
        if (($y + $height) > $allowed_height) {
            $y = $this->add_page(false);
        }

        // Draw top border
        $this->Line(40, ($y-2), 560, ($y-2), array('color' => array(192, 192, 192)));
        $y += 10;

        foreach ($this->totals as $key => $value) {
            if (is_null($value['value'])) {
                continue;
            }

            // Check if we need to show tax rows
            if ($key == 'taxes' && ($this->invoiceOptions['woo_pdf_list_tax'] == 0 || ($this->invoiceOptions['woo_pdf_list_tax'] == 2 && $this->invoiceOptions['woo_pdf_display_tax_inline'] == 0 && $this->multiple_tax_rates))) {
                continue;
            }

            // Check if tax has been displayed inline - in this case only shipping tax needs to be displayed here
            if ($key == 'taxes' && $this->multiple_tax_rates && $this->invoiceOptions['woo_pdf_list_tax'] == '2') {

                if (RightPress_WC_Legacy::order_get_shipping_total($this->orderData) && $this->orderData->get_shipping_tax()) {

                        // Get shipping tax rate
                        $shipping_tax_rate = $this->get_item_tax_rate((double) RightPress_WC_Legacy::order_get_shipping_total($this->orderData), (double) $this->orderData->get_shipping_tax());

                        // Display shipping tax
                        $field_name = $this->invoiceOptions['woo_pdf_title_shipping_tax'] . ' (' . floatval($shipping_tax_rate) . ' %)';
                        $this->MultiCell(140, $r, $this->decode($field_name), $this->showBorders, 'L', 0, 1, 340, $y, true, 0, false, false, $r, 'T', false);
                        $this->MultiCell(80, $r, $this->format_currency((double) $this->orderData->get_shipping_tax()), $this->showBorders, 'R', 0, 1, 480, $y, true, 0, false, false, $r, 'T', false);
                        $y += $r + $s;
                }

                continue;
            }

            // If we reached this point, proceed normally
            if (is_array($value['value'])) {
                foreach ($value['value'] as $code => $field) {
                    $field_name = (isset($field['rate'])) ? $field['name'] . ' (' . floatval($field['rate']) . ' %)' : $field['name'];
                    $this->MultiCell(140, $r, $this->decode($field_name), $this->showBorders, 'L', 0, 1, 340, $y, true, 0, false, false, $r, 'T', false);
                    $this->MultiCell(80, $r, $this->format_currency($field['amount']), $this->showBorders, 'R', 0, 1, 480, $y, true, 0, false, false, $r, 'T', false);
                    $y += $r + $s;
                }

                continue;
            }

            // Set up totals row
            $auto_padding = ($key != 'total') ? false : true;
            $borders_name = ($key != 'total') ? $this->showBorders : array('LTB' => array('width' => 1, 'color' => array(0, 0, 0)));
            $borders_value = ($key != 'total') ? $this->showBorders : array('TRB' => array('width' => 1, 'color' => array(0, 0, 0)));

            // Name
            $name = ($key == 'total') ? $value['name'] : $value['name'];
            $this->MultiCell(140, $r, $this->decode($name), $borders_name, 'L', 0, 1, 340, $y, true, 0, false, $auto_padding, $r, 'T', false);

            // Value
            $this->MultiCell(80, $r, $this->format_currency($value['value']), $borders_value, 'R', 0, 1, 480, $y, true, 0, false, $auto_padding, $r, 'T', false);

            $y += $r + $s + ($key == 'total' ? 10 : 0);

            $this->style();
        }

        return $y;

    }

    /**
     * Render left block
     *
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @param string $type
     * @return int
     */
    public function render_left_block($y, $r, $s, $type)
    {
        // Define blocks
        $blocks = array(
            'amount_in_words' => array(
                'title' => $this->invoiceOptions['woo_pdf_title_amount_in_words'],
                'text' => $this->get_amount_in_words((double) RightPress_WC_Legacy::order_get_total($this->orderData), $this->invoiceOptions),
            ),
        );

        // Define all custom blocks
        foreach (array('1', '2', '3', '4') as $n) {
            $blocks['block_'.$n] = array(
                'title' => $this->replace_macros($this->invoiceOptions['woo_pdf_block_'.$n.'_title'], false),
                'text' => $this->replace_macros($this->invoiceOptions['woo_pdf_block_'.$n.'_content'], $this->invoiceOptions['woo_pdf_block_'.$n.'_remove_empty_lines']),
            );
        }

        // Break down the block to strings
        $block_strings = explode(PHP_EOL, $this->decode($blocks[$type]['text']));

        // Do not print header if no content is present
        $block_is_empty = true;

        foreach ($block_strings as $string) {

            $string = trim($string);

            if (!empty($string)) {
                $block_is_empty = false;
                break;
            }
        }

        if ($block_is_empty) {
            return $this->GetY();
        }

        // Get allowed height
        $allowed_height = $this->current_page == 1 ? $this->max_first_page_height : $this->max_page_height;

        $y += 10;
        $this->style('b');
        $this->MultiCell(520, $r, $this->decode($blocks[$type]['title']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 20, 'M', false);
        $this->style();
        $y += 15 + $s;

        // Iterate through strings
        foreach ($block_strings as $string) {

            $current_content_height = $this->get_height('block', $string, $s);

            if (($y + (20 + 15 + $s + $current_content_height + 20)) > $allowed_height) {
                $y = $this->add_page(false);
            }

            $this->MultiCell(520, 0, $string, $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 0, 'T', false);
            $y = $this->GetY();
        }

        return $this->GetY();
    }

    /**
     * Add new page
     *
     * @access public
     * @param bool $has_products
     * @return int
     */
    public function add_page($has_products = true)
    {
        // Add footer to current page
        $this->render_footer();

        // Add new page
        $this->AddPage();

        // Print header
        $this->render_page_header();

        $this->style('gray');
        $this->MultiCell(100, 12, $this->decode($this->invoiceOptions['woo_pdf_title_additional_page']), $this->showBorders, 'L', 0, 1, 40, 40, true, 0, false, false, 0, 'M', false);
        $this->style();

        if ($has_products) {
            $this->render_table_header(120);
            $this->style();
        }

        // Increment counters
        $this->current_page = $this->page_count + 1;
        $this->page_count++;

        // Reset y
        $y = $has_products ? 145 : 130;

        return $y;
    }

    /**
     * Render page numbers on all pages
     *
     * @access public
     * @return void
     */
    public function render_page_numbers()
    {
        $page_number = $this->page_count;
        while ($page_number) {
            $this->setPage($page_number);
            $this->style('gray');
            $this->MultiCell(100, 12, $this->decode($this->invoiceOptions['woo_pdf_title_page']) . ' ' . $page_number . ' / ' . $this->page_count, $this->showBorders, 'R', 0, 1, 460, 790, true, 0, false, false, 0, 'M', false);
            $this->style();
            $page_number--;
        }
    }

    /**
     * Try to get seller logo
     *
     * @access public
     * @return mixed
     */
    public function get_seller_logo()
    {
        $logo = $this->invoiceOptions['woo_pdf_seller_logo'];

        if (empty($logo)) {
            return false;
        }

        // If allow_url_fopen is active, use getimagesize to detect image
        if (ini_get('allow_url_fopen')) {

            if (is_array(getimagesize($logo))) {
                return $logo;
            }

            return false;
        }

        // Otherwise, get image by CURL, use imagecreatefromstring to detect image
        else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $logo);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $logo_data = curl_exec($ch);

            if (imagecreatefromstring($logo_data)) {
                return $logo;
            }

            return false;
        }
    }

    /**
     * Render first page header
     *
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return void
     */
    public function render_first_page_header($y, $r, $s)
    {
        $y_right_block = $y;

        // Show seller details block
        $y = $this->render_seller_details($y, $r, $s);

        // Space between seller and buyer details
        $y = $y + 20;

        // Show buyer details block
        $y = $this->render_buyer_details($y, $r, $s);

        // Show logo if set
        if ($seller_logo = $this->get_seller_logo()) {
            $resize_factor = (isset($this->invoiceOptions['woo_pdf_seller_logo_resize']) && is_numeric($this->invoiceOptions['woo_pdf_seller_logo_resize'])) ? ($this->invoiceOptions['woo_pdf_seller_logo_resize'] / 100) : 1;
            $this->Image($seller_logo, 400, $y_right_block, (150 * $resize_factor), (100 * $resize_factor), '', '', 'T', true, 300, 'R', false, false, $this->showBorders, true, false, false, false, false);
            $y_right_block = $this->getImageRBY();
        }
        else {
            $y_right_block = 40;
        }

        if (($y_right_block + 20 + 40) < $y) {
            $y_right_block = $y - 40;
        }
        else {
            $y_right_block = $y_right_block + 20;
        }

        // Invoice details
        $invoice_info_content = $this->replace_macros($this->invoiceOptions['woo_pdf_invoice_info_content'], $this->invoiceOptions['woo_pdf_invoice_info_remove_empty_lines']);
        $inv_strings = explode(PHP_EOL, $invoice_info_content);
        $first_line = true;

        // Iterate through strings
        foreach ($inv_strings as $string) {

            // Make first string bold
            if ($first_line) {
                $first_line = false;
                $this->style('b');
                // leaving $y_right_block unchanged
            }

            // And default style for others
            else {
                $this->style();
                $y_right_block = $this->GetY() + 2;
            }

            // Explode string to 2 parts for left and right columns
            $stringarr = explode('|', $string);

            // Print left column if set
            if (isset($stringarr[0])) {
                $this->MultiCell(140, 0, $stringarr[0], $this->showBorders, 'L', 0, 1, 340, $y_right_block, true, 0, false, false, 80, 'T', false);
            }

            // Print right column if set
            if (isset($stringarr[1])) {
                $this->MultiCell(80, 0, $stringarr[1], $this->showBorders, 'R', 0, 1, 480, $y_right_block, true, 0, false, false, 80, 'T', false);

            }
        }

        return $this->GetY();
    }

    /**
     * Render page header (all pages except first)
     *
     * @access public
     * @return void
     */
    public function render_page_header()
    {

        // Invoice number
        $this->style('b');
        $document_name = $this->invoiceType == 'invoice' ? $this->invoiceOptions['woo_pdf_document_name'] : $this->invoiceOptions['woo_pdf_proforma_name'];
        $this->MultiCell(145, 0, $this->decode($document_name), $this->showBorders, 'L', 0, 1, 320, 40, true, 0, false, false, 80, 'T', false);
        $this->MultiCell(95, 0, $this->decode($this->invoiceInfo['prefix']) . ' ' . $this->invoiceInfo['id'] . ' ' . $this->decode($this->invoiceInfo['suffix']), $this->showBorders, 'R', 0, 1, 465, 40, true, 0, false, false, 80, 'T', false);

        // Invoice date
        $this->style();
        $this->MultiCell(145, 0, $this->decode($this->invoiceOptions['woo_pdf_title_date']), $this->showBorders, 'L', 0, 1, 320, 55, true, 0, false, false, 80, 'T', false);
        $this->MultiCell(95, 0, $this->date(), $this->showBorders, 'R', 0, 1, 465, 55, true, 0, false, false, 80, 'T', false);

        // Order amount
        $this->style();
        $this->MultiCell(145, 0, $this->decode($this->invoiceOptions['woo_pdf_title_amount']) . ' (' . $this->decode($this->currency(false)) . ')', $this->showBorders, 'L', 0, 1, 320, 68, true, 0, false, false, 80, 'T', false);
        $this->MultiCell(95, 0, $this->format_currency($this->total()), $this->showBorders, 'R', 0, 1, 465, 68, true, 0, false, false, 80, 'T', false);

    }

    /**
     * Render seller details block (6 lines at most)
     *
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return void
     */
    public function render_seller_details($y, $r, $s)
    {
        // Seller block title
        $this->style('gray');
        $this->MultiCell(280, 0, $this->decode($this->invoiceOptions['woo_pdf_title_seller']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 0, 'T', false);

        // Set black bold font
        $this->style(array('size' => 8, 'style' => 'b'));

        // Seller name
        if (!empty($this->invoiceOptions['woo_pdf_seller_name'])) {
            $this->MultiCell(280, 0, $this->decode($this->invoiceOptions['woo_pdf_seller_name']), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);
        }

        // Seller details
        $this->style(array('size' => 8));
        $this->MultiCell(280, 0, $this->decode($this->invoiceOptions['woo_pdf_seller_content']), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);

        $y = $this->GetY();
        $y = ($y < 100) ? 100 : $y;

        return $y;
    }

    /**
     * Render buyer details block (7 lines at most)
     *
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return void
     */
    public function render_buyer_details($y, $r, $s)
    {
        $buyer_content = '';

        // Seller block title
        $this->style('gray');
        $this->MultiCell(280, 0, $this->decode($this->invoiceOptions['woo_pdf_title_buyer']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 0, 'T', false);

        // Set bold font
        $this->style('b');

        // Buyer company and name
        $name = array();

        $billing_first_name = RightPress_WC_Legacy::order_get_billing_first_name($this->orderData);
        $billing_last_name = RightPress_WC_Legacy::order_get_billing_last_name($this->orderData);
        $billing_company = RightPress_WC_Legacy::order_get_billing_company($this->orderData);

        if (!empty($billing_first_name)) {
            array_push($name, $billing_first_name);
        }
        if (!empty($billing_last_name)) {
            array_push($name, $billing_last_name);
        }

        if (!empty($billing_company)) {
            $this->MultiCell(280, 0, $this->decode($billing_company), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);
            if (!empty($name)) {
                $buyer_content .= join(' ', $name) . PHP_EOL;
            }
        }
        else if (!empty($name)) {
            $this->MultiCell(280, 0, $this->decode(join(' ', $name)), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);
        }

        // Buyer details
        $buyer_content .= $this->replace_macros($this->invoiceOptions['woo_pdf_buyer_content'], $this->invoiceOptions['woo_pdf_buyer_remove_empty_lines']);

        $this->style();
        $this->MultiCell(280, 0, $this->decode($buyer_content), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);

        $y = $this->GetY();
        $y = ($y < 180) ? 180 : $y;

        return $y;
    }

    /**
     * Render page footer
     *
     * @access public
     * @return void
     */
    public function render_footer($is_test = false)
    {
        if (!empty($this->invoiceOptions['woo_pdf_footer'])) {

            $start_y = $is_test ? 0 : (780 - $this->footer_height);

            $this->style('footer');
            $this->Line(40, $start_y, 560, $start_y, array('color' => array(192, 192, 192)));
            $this->MultiCell(520, 0, $this->decode($this->replace_macros($this->invoiceOptions['woo_pdf_footer'], $this->invoiceOptions['woo_pdf_footer_remove_empty_lines'])), $this->showBorders, 'C', 0, 1, 40, ($start_y + 10), true, 0, false, false, 500, 'T', false);
        }
    }


    /**
     * Set text style
     *
     * @access public
     * @param string/array $input
     * @return void
     */
    public function style($input = null)
    {
        // General style
        $style = array(
                'family' => 'arial',
                'style' => '',
                'size' => 9,
                'color' => '#000000'
        );

        // Define styles
        $styles = array(
            'b' => array(
                'style' => 'b',
            ),
            'gray' => array(
                'size' => 8,
                'color' => '#808080',
            ),
            'footer' => array(
                'size' => 8,
            ),
        );

        // Make requested style
        if (!empty($input) && !is_array($input)) {
            $style = array_merge($style, $styles[$input]);
        }
        else if (!empty($input) && is_array($input)) {
            $style = array_merge($style, $input);
        }

        // Allow to override styles
        $style = apply_filters('woo_pdf_styles', $style);

        list($r, $g, $b) = $this->hex_rgb($style['color']);

        $this->SetFont($style['family'], $style['style'], $style['size']);
        $this->SetTextColor($r, $g, $b);
    }


    /**
     * Convert hex color to rgb
     *
     * @access public
     * @param string $hex
     * @return array
     */
    public function hex_rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
           $r = hexdec(substr($hex,0,1).substr($hex,0,1));
           $g = hexdec(substr($hex,1,1).substr($hex,1,1));
           $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
           $r = hexdec(substr($hex,0,2));
           $g = hexdec(substr($hex,2,2));
           $b = hexdec(substr($hex,4,2));
        }

        return array($r, $g, $b);
    }

    /**
     * Get formatted date
     *
     * @access public
     * @param string $real_date
     * @return string
     */
    public function date($real_date = false)
    {
        $formats = array(
            '0' => 'n/j/y',
            '1' => 'n/j/Y',
            '2' => 'j/n/y',
            '3' => 'j/n/Y',
            '4' => 'y-m-d',
            '5' => 'Y-m-d',
            '6' => 'F j, Y',
            '7' => 'd.m.Y',
            '8' => 'd-m-Y',
        );

        $format = apply_filters('woo_pdf_date_format', $formats[$this->invoiceOptions['woo_pdf_date_format']]);

        if ($this->invoiceType == 'proforma' || $real_date) {
            $order_date_timestamp = strtotime(RightPress_WC_Legacy::order_get_date_created($this->orderData));
            return date($format, $order_date_timestamp);
        }
        else {
            return date($format);
        }
    }

    /**
     * Get order total amount
     *
     * @access public
     * @return double
     */
    public function total()
    {
        return (double) RightPress_WC_Legacy::order_get_total($this->orderData);
    }

    /**
     * Format amount
     *
     * @access public
     * @param double $amount
     * @param int $decimals
     * @return string
     */
    public function format_amount($amount, $decimals = null)
    {
        $decimals = is_null($decimals) ? get_option('woocommerce_price_num_decimals') : $decimals;

        return number_format(
                   $amount,
                   $decimals,
                   wc_get_price_decimal_separator(),
                   wc_get_price_thousand_separator()
               );
    }

    /**
     * Format amount with currency symbol
     *
     * @access public
     * @param double $amount
     * @return string
     */
    public function format_currency($amount, $decimals = null)
    {
        $is_negative = $amount < 0;
        $amount = abs($amount);

        if ($this->invoiceOptions['woo_pdf_display_currency_symbol'] && $currency_symbol = $this->decode($this->currency(true))) {
            // Add currency symbol
            $amount = preg_replace('/&nbsp;/', ' ', sprintf(get_woocommerce_price_format(), $currency_symbol, $this->format_amount((double) $amount, $decimals)));
        }
        else {
            $amount = $this->format_amount((double) $amount, $decimals);
        }

        return $is_negative ? '-' . $amount : $amount;
    }

    /**
     * Decode HTML special entities back to characters
     *
     * @access public
     * @param string $string
     * @return string
     */
    public function decode($string)
    {
        return html_entity_decode(htmlspecialchars_decode($string, ENT_QUOTES), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get order item meta information
     *
     * @access public
     * @param mixed $item
     * @return string
     */
    public function item_meta($item)
    {
        // Legacy handling
        if (!RightPress_Helper::wc_version_gte('3.0')) {
            $item_obj = new WC_Order_Item_Meta($item['item_meta']);
            return $this->item_meta_legacy($item_obj);
        }

        $meta = array();
        $meta_string = '';

        foreach ($item->get_meta_data() as $meta_value) {

            if (substr($meta_value->key, 0, 1) == '_' || is_serialized($meta_value->value)) {
                continue;
            }

            $value = $meta_value->value;

            if (taxonomy_exists(esc_attr(str_replace('attribute_', '', $meta_value->key)))) {
                $term = get_term_by('slug', $value, esc_attr(str_replace('attribute_', '', $meta_value->key)));
                if (!is_wp_error($term) && $term->name) {
                    $value = $term->name;
                }
            }

            $meta[wc_attribute_label(str_replace('attribute_', '', $meta_value->key))][] = $value;
        }

        $meta_final = array();

        // Glue values together
        foreach ($meta as $key => $values) {
            $meta_final[] = $key . ': ' . join(', ', $values) . '.';
        }

        // Make string with each property in separate line
        $meta_string = join(PHP_EOL, $meta_final);

        return $meta_string;
    }

    /**
     * Get order item meta information
     *
     * @access public
     * @param object $item
     * @return string
     */
    public function item_meta_legacy($item)
    {
        global $woocommerce;

        if (empty($item->meta)) {
            return;
        }

        $meta = array();
        $meta_string = '';

        foreach ($item->meta as $key => $values) {

            if (empty($values) || substr($key, 0, 1) == '_') {
                continue;
            }

            foreach ($values as $value) {

                if (is_serialized($value)) {
                    continue;
                }

                if (taxonomy_exists(esc_attr(str_replace('attribute_', '', $key)))) {
                    $term = get_term_by('slug', $value, esc_attr(str_replace('attribute_', '', $key)));
                    if (!is_wp_error($term) && $term->name) {
                        $value = $term->name;
                    }
                }

                $meta[wc_attribute_label(str_replace('attribute_', '', $key))][] = $value;
            }

        }

        $meta_final = array();

        // Glue values together
        foreach ($meta as $key => $values) {
            $meta_final[] = $key . ': ' . join(', ', $values) . '.';
        }

        // Make string with each property in separate line
        $meta_string = join(PHP_EOL, $meta_final);

        return $meta_string;
    }

    /**
     * Get single row total (excl. taxes)
     *
     * @access public
     * @param object $item
     * @param object $order
     * @return int
     */
    public function row_total($item, $order)
    {
        $line_subtotal = ($this->invoiceOptions['woo_pdf_inclusive_tax'] || ($this->multiple_tax_rates && $this->invoiceOptions['woo_pdf_display_tax_inline'] == 0) || $this->invoiceOptions['woo_pdf_display_tax_inline'] == 1) ? (RightPress_WC_Legacy::order_item_get_subtotal($item) + RightPress_WC_Legacy::order_item_get_subtotal_tax($item)) : RightPress_WC_Legacy::order_item_get_subtotal($item);

        return (double) $line_subtotal;
    }

    /**
     * Calculate single item price (excl. taxes)
     *
     * @access public
     * @param object $item
     * @param object $order
     * @return string
     */
    public function item_price($item, $order)
    {
        $line_subtotal = $this->invoiceOptions['woo_pdf_inclusive_tax'] ? ((double) RightPress_WC_Legacy::order_item_get_subtotal($item) + (double) RightPress_WC_Legacy::order_item_get_subtotal_tax($item)) : (double) RightPress_WC_Legacy::order_item_get_subtotal($item);

        $price = $line_subtotal / (int) RightPress_WC_Legacy::order_item_get_quantity($item);

        if ((((double) $price) * (int) RightPress_WC_Legacy::order_item_get_quantity($item)) == (double) $this->row_total($item, $this->orderData)) {
            return $price;
        }
        else {
            return $price;
        }
    }

    /**
     * Get unformatted subtotals, taxes and totals
     *
     * @access public
     * @return array
     */
    public function get_totals()
    {
        global $woocommerce;

        $totals = array();

        /**
         * SUBTOTAL
         */
        $subtotal = 0;

        foreach ($this->orderData->get_items() as $item) {
            if ($this->invoiceOptions['woo_pdf_inclusive_tax'] || ($this->multiple_tax_rates && $this->invoiceOptions['woo_pdf_display_tax_inline'] == 0) || $this->invoiceOptions['woo_pdf_display_tax_inline'] == 1) {
                $subtotal += floatval(RightPress_WC_Legacy::order_item_get_subtotal($item));
                $subtotal += floatval(RightPress_WC_Legacy::order_item_get_subtotal_tax($item));
            }
            else {
                $subtotal += $this->orderData->get_line_subtotal($item);
            }
        }

        $totals['subtotal'] = array(
            'name'  => $this->invoiceOptions['woo_pdf_title_subtotal'],
            'value' => (double) $subtotal,
        );

        /**
         * CART DISCOUNT
         */
        $cart_discount = (double) RightPress_WC_Legacy::order_get_total_discount($this->orderData);

        // Check if cart discount is set
        if ($cart_discount != 0) {

            // Check if tax needs to be included
            if ($this->invoiceOptions['woo_pdf_inclusive_tax']) {
                $cart_discount_tax = (double) RightPress_WC_Legacy::order_get_discount_tax($this->orderData);
                $cart_discount = $cart_discount > 0 ? ($cart_discount + $cart_discount_tax) : ($cart_discount - $cart_discount_tax);
            }

            // Add to array
            $totals['cart_discount'] = array(
                'name'  => $this->invoiceOptions['woo_pdf_title_cart_discount'],
                'value' => $cart_discount,
            );
        }
        else {
            $totals['cart_discount'] = null;
        }

        /**
         * SHIPPING
         */
        if (RightPress_WC_Legacy::order_get_shipping_total($this->orderData) > 0) {
            $totals['shipping']  = (double) RightPress_WC_Legacy::order_get_shipping_total($this->orderData);
        }
        else if (RightPress_WC_Legacy::order_get_shipping_total($this->orderData) == 0 && $this->invoiceOptions['woo_pdf_display_free_shipping']) {
            $totals['shipping']  = (double) 0;
        }
        else {
            $totals['shipping']  = null;
        }

        if ($totals['shipping'] != null && $this->invoiceOptions['woo_pdf_inclusive_tax']) {
            $totals['shipping'] += (double) $this->orderData->get_shipping_tax();
        }

        if (!is_null($totals['shipping'])) {
            $totals['shipping'] = array(
                'name'  => $this->invoiceOptions['woo_pdf_title_shipping'],
                'value' => $totals['shipping'],
            );
        }

        /**
         * FEES
         */
        $totals['fees'] = array();

        if ($fees = $this->orderData->get_fees()) {
            foreach ($fees as $id => $fee) {
                $totals['fees'][$id] = array(
                    'name'      => $fee['name'],
                    'amount'    => (double) $fee['line_total']
                );

                if ($this->invoiceOptions['woo_pdf_inclusive_tax']) {
                    $totals['fees'][$id]['amount'] += (double) $fee['line_tax'];
                }
            }
        }

        if (empty($totals['fees'])) {
            $totals['fees'] = null;
        }

        if (!is_null($totals['fees'])) {
            $totals['fees'] = array(
                'name' => 'Fees',
                'value' => $totals['fees'],
            );
        }

        /**
         * TAXES
         */
        // Calculate order tax
        if (is_null($this->order_tax)) {
            $this->calculate_order_tax();
        }

        $totals_taxes = array();

        foreach ($this->order_tax as $tax_key => $tax) {
            $totals_taxes[$tax_key] = array(
                'name'  => RightPress_WC_Legacy::order_item_tax_get_name($tax),
                'amount'    => ((float) RightPress_WC_Legacy::order_item_tax_get_tax_total($tax) + (float) RightPress_WC_Legacy::order_item_tax_get_shipping_tax_total($tax))
            );

            if (isset($this->tax_rates[$tax_key])) {
                $totals_taxes[$tax_key]['rate'] = (float) $this->tax_rates[$tax_key];
            }
        }

        if (empty($totals_taxes)) {
            $totals_taxes = null;
        }

        if (!is_null($totals_taxes)) {
            $totals_taxes = array(
                'name'  => 'Taxes',
                'value' => $totals_taxes,
            );
        }

        /**
         *  Display taxes before total only if subtotal is displayed excluding taxes (so values sum up nicely)
         */
        if ($totals_taxes && !$this->invoiceOptions['woo_pdf_inclusive_tax']) {
            $totals['taxes'] = $totals_taxes;
        }

        /**
         * TOTAL
         */
        $totals['total'] = array(
            'name'  => $this->invoiceOptions['woo_pdf_title_total'],
            'value' => (double) RightPress_WC_Legacy::order_get_total($this->orderData),
        );

        /**
         *  Display taxes below total only if subtotal is displayed including taxes
         */
        if ($totals_taxes && !isset($totals['taxes'])) {
            $totals['taxes'] = $totals_taxes;
        }

        /**
         * Optional total excl. tax row
         */
        if ($this->invoiceOptions['woo_pdf_total_excl_tax']) {
            $totals['total_excl_tax'] = array(
                'name'  => $this->invoiceOptions['woo_pdf_title_total_excl_tax'],
                'value' => (double) ($this->orderData->get_total() - $this->orderData->get_total_tax()),
            );
        }

        // Allow developers to override totals
        return apply_filters('woo_pdf_totals', $totals, $this->orderData, $this->invoiceOptions);
    }

    /**
     * Check if order contains different tax rates and return totals for each tax rate if so
     *
     * @access public
     * @return mixed
     */
    public function maybe_get_multiple_tax_totals()
    {
        // Get order items
        $items = $this->orderData->get_items();

        $result = array();

        // Iterate over all items and extract tax rates and tax subtotals
        foreach ($items as $item) {
            $tax_rate = $this->get_item_tax_rate(RightPress_WC_Legacy::order_item_get_subtotal($item), RightPress_WC_Legacy::order_item_get_subtotal_tax($item));
            $result[(string) $tax_rate] = RightPress_WC_Legacy::order_item_get_subtotal_tax($item) + (isset($result[(string) $tax_rate]) ? $result[(string) $tax_rate] : 0);
        }

        // Do we have more than one tax rate in use
        if (!empty($result) && count($result) > 1) {
            return $result;
        }

        return false;
    }

    /**
     * Get tax rate for single item
     *
     * @access public
     * @param $line_subtotal
     * @param $line_subtotal_tax
     * @return float
     */
    public function get_item_tax_rate($line_subtotal, $line_subtotal_tax)
    {
        if (empty($line_subtotal) || !is_numeric($line_subtotal)) {
            return 0;
        }

        if (empty($line_subtotal_tax) || !is_numeric($line_subtotal_tax)) {
            $line_subtotal_tax = 0;
        }

        // Calculate order tax
        if (is_null($this->order_tax)) {
            $this->calculate_order_tax();
        }

        // Calculate current tax rate from subtotal and subtotal tax
        if ($line_subtotal != 0) {
            $tax_rate = $line_subtotal_tax / $line_subtotal * 100;
        }
        else {
            $tax_rate = 0;
        }

        // Due to rounding we may have incorrect tax rate - attempt to fix by matching with tax rates from DB
        $tax_rate_fixed = $tax_rate;
        $tax_rate_fix_amount = 0;

        foreach ($this->order_tax as $tax_key => $tax) {

            if (!isset($this->tax_rates[$tax_key])) {
                continue;
            }

            if (($this->tax_rates[$tax_key] > $tax_rate && $this->tax_rates[$tax_key] < ($tax_rate + 0.49)) || ($this->tax_rates[$tax_key] < $tax_rate && $this->tax_rates[$tax_key] > ($tax_rate - 0.49))) {
                if ($tax_rate_fix_amount == 0 || abs($this->tax_rates[$tax_key] - $tax_rate) < $tax_rate_fix_amount) {
                    $tax_rate_fixed = $this->tax_rates[$tax_key];
                    $tax_rate_fix_amount = abs($this->tax_rates[$tax_key] - $tax_rate);
                }
            }
        }

        return round((float) $tax_rate_fixed, 2);
    }

    /**
     * Get order tax and tax rates
     *
     * @access public
     * @return array
     */
    public function calculate_order_tax()
    {
        // Get tax rows
        $this->order_tax = $this->orderData->get_items('tax');
        $this->tax_rates = array();

        // Nothing to calculate
        if (empty($this->order_tax)) {
            return;
        }

        global $wpdb;

        // Find rates
        $all_rates = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'woocommerce_tax_rates');

        // No rates found
        if (!is_array($all_rates) || empty($all_rates)) {
            return;
        }

        // Find matches
        foreach ($this->order_tax as $single_tax_key => $single_tax) {

            $rate_id = RightPress_WC_Legacy::order_item_tax_get_rate_id($single_tax);

            if (!RightPress_Helper::is_empty($rate_id)) {
                foreach ($all_rates as $rate) {
                    if ($rate_id == $rate->tax_rate_id) {
                        $this->tax_rates[$single_tax_key] = $rate->tax_rate_id;
                    }
                }
            }
        }
    }

    /**
     * Replace macros in text
     *
     * @access public
     * @param string $string
     * @param bool $remove_empty_lines
     * @return string
     */
    public function replace_macros($string, $remove_empty_lines = false)
    {
        // List supported macros and get their values
        $macros = array(
            '{{order_id}}'              => $this->orderData->get_order_number(),
            '{{order_date}}'            => $this->date(true),
            '{{customer_id}}'           => RightPress_WC_Legacy::order_get_customer_id($this->orderData),
            '{{customer_note}}'         => RightPress_WC_Legacy::order_get_customer_note($this->orderData),
            '{{payment_method}}'        => RightPress_WC_Legacy::order_get_payment_method_title($this->orderData),
            '{{shipping_method}}'       => $this->orderData->get_shipping_method(),
            '{{billing_first_name}}'    => RightPress_WC_Legacy::order_get_billing_first_name($this->orderData),
            '{{billing_last_name}}'     => RightPress_WC_Legacy::order_get_billing_last_name($this->orderData),
            '{{billing_company}}'       => RightPress_WC_Legacy::order_get_billing_company($this->orderData),
            '{{billing_address_1}}'     => RightPress_WC_Legacy::order_get_billing_address_1($this->orderData),
            '{{billing_address_2}}'     => RightPress_WC_Legacy::order_get_billing_address_2($this->orderData),
            '{{billing_city}}'          => RightPress_WC_Legacy::order_get_billing_city($this->orderData),
            '{{billing_postcode}}'      => RightPress_WC_Legacy::order_get_billing_postcode($this->orderData),
            '{{billing_country}}'       => RightPress_WC_Legacy::order_get_billing_country($this->orderData),
            '{{billing_state}}'         => RightPress_WC_Legacy::order_get_billing_state($this->orderData),
            '{{billing_email}}'         => RightPress_WC_Legacy::order_get_billing_email($this->orderData),
            '{{billing_phone}}'         => RightPress_WC_Legacy::order_get_billing_phone($this->orderData),
            '{{shipping_first_name}}'   => RightPress_WC_Legacy::order_get_shipping_first_name($this->orderData),
            '{{shipping_last_name}}'    => RightPress_WC_Legacy::order_get_shipping_last_name($this->orderData),
            '{{shipping_company}}'      => RightPress_WC_Legacy::order_get_shipping_company($this->orderData),
            '{{shipping_address_1}}'    => RightPress_WC_Legacy::order_get_shipping_address_1($this->orderData),
            '{{shipping_address_2}}'    => RightPress_WC_Legacy::order_get_shipping_address_2($this->orderData),
            '{{shipping_city}}'         => RightPress_WC_Legacy::order_get_shipping_city($this->orderData),
            '{{shipping_postcode}}'     => RightPress_WC_Legacy::order_get_shipping_postcode($this->orderData),
            '{{shipping_country}}'      => RightPress_WC_Legacy::order_get_shipping_country($this->orderData),
            '{{shipping_state}}'        => RightPress_WC_Legacy::order_get_shipping_state($this->orderData),
            '{{woo_pdf_invoice_id}}'    => (!empty($this->invoiceInfo['prefix']) ? $this->decode($this->invoiceInfo['prefix']) . ' ' : '') . $this->invoiceInfo['id'] . (!empty($this->invoiceInfo['suffix']) ? ' ' . $this->decode($this->invoiceInfo['suffix']) : ''),
            '{{invoice_number}}'        => (!empty($this->invoiceInfo['prefix']) ? $this->decode($this->invoiceInfo['prefix']) . ' ' : '') . $this->invoiceInfo['id'] . (!empty($this->invoiceInfo['suffix']) ? ' ' . $this->decode($this->invoiceInfo['suffix']) : ''),
            '{{invoice_date}}'          => $this->date(),
            '{{order_currency}}'        => $this->decode($this->currency(false)),
            '{{order_amount}}'          => $this->format_currency($this->total()),
            '{{invoice_title}}'         => $this->decode($this->invoiceType == 'invoice' ? $this->invoiceOptions['woo_pdf_document_name'] : $this->invoiceOptions['woo_pdf_proforma_name']),
            '{{invoice_date_title}}'    => $this->decode($this->invoiceOptions['woo_pdf_title_date']),
            '{{order_amount_title}}'    =>  $this->decode($this->invoiceOptions['woo_pdf_title_amount']),
        );

        // Allow custom macros
        $macros = apply_filters('woo_pdf_macros', $macros, $this->orderData);

        // Split entire block into array line by line
        $lines = explode(PHP_EOL, $string);
        $processed_lines = array();

        // Load WC countries to replace country macros
        if (!$this->wc_countries) {
            $this->wc_countries = new WC_Countries();
        }

        // Iterate over all lines and replace macros
        foreach ($lines as $line_key => $line) {

            $macros_found = 0;
            $macros_not_empty = 0;
            $processed_line = $line;

            // Search for regular macros and replace them
            foreach ($macros as $macro => $value) {

                $replace_count = 0;

                // Convert two-letter country code to full country name
                if (in_array($macro, array('{{billing_country}}', '{{shipping_country}}')) && $this->wc_countries && isset($this->wc_countries->countries[$value])) {

                    // Also maybe save the states
                    if (isset($this->wc_countries->states[$value])) {
                        $states = $this->wc_countries->states[$value];
                    }

                    $value = $this->wc_countries->countries[$value];
                }

                // Convert state code to full state name
                if (in_array($macro, array('{{billing_state}}', '{{shipping_state}}')) && isset($states[$value])) {
                    $value = $states[$value];
                }

                $processed_line = preg_replace('/'.$macro.'/', preg_replace('/\$/', '\\\$', $value), $processed_line, -1, $replace_count);

                $macros_found = $macros_found + $replace_count;

                if ($value != '') {
                    $macros_not_empty = $macros_not_empty + $replace_count;
                }
            }

            // Search for custom macros and attempt to replace them
            preg_match_all('/\{\{.+(?:\}\})/U', $processed_line, $matches);

            if (!empty($matches) && !empty($matches[0])) {
                foreach ($matches[0] as $match) {

                    $key = preg_replace('/\{\{(.+)\}\}/', '$1', $match);

                    // Check in standard custom fields
                    if (isset($this->orderData->order_custom_fields[$key]) || isset($this->orderData->order_custom_fields['_'.$key])) {

                        $key = isset($this->orderData->order_custom_fields[$key]) ? $key : '_' . $key;

                        $macros_found++;

                        $value = is_array($this->orderData->order_custom_fields[$key]) ? $this->orderData->order_custom_fields[$key][0] : $this->orderData->order_custom_fields[$key];

                        if ($value != '') {
                            $macros_not_empty++;
                        }

                        $processed_line = preg_replace('/'.$match.'/', preg_replace('/\$/', '\\\$', $value), $processed_line);
                    }

                    // Check in order meta
                    else {
                        $buyer_custom_field = RightPress_WC_Legacy::order_get_meta($this->orderData, $key, true);

                        if ($buyer_custom_field == '') {
                            $buyer_custom_field = RightPress_WC_Legacy::order_get_meta($this->orderData, ('_' . $key), true);
                        }

                        if ($buyer_custom_field != '') {
                            $macros_found++;
                            $macros_not_empty++;
                            $processed_line = preg_replace('/'.$match.'/', preg_replace('/\$/', '\\\$', $buyer_custom_field), $processed_line);
                        }
                    }
                }
            }

            if (!$remove_empty_lines || $macros_found == 0 || $macros_not_empty != 0) {
                $processed_lines[] = $processed_line;
            }
        }

        // Join lines
        $string = join(PHP_EOL, $processed_lines);

        // Remove all macros that were not found
        $string = preg_replace('/\{\{.+(?:\}\})/U', '', $string);

        return $string;
    }

    /**
     * Return currency symbol
     *
     * @param bool $symbol
     * @return string
     */
    public function currency($symbol = true)
    {
        $currencies_symbols_list = array(
            'ALL' => 'Lek',
            'AFN' => '؋',
            'ARS' => '$',
            'AWG' => 'ƒ',
            'AUD' => '$',
            'AZN' => 'ман',
            'BSD' => '$',
            'BBD' => '$',
            'BYR' => 'p.',
            'BZD' => 'BZ$',
            'BMD' => '$',
            'BOB' => '$b',
            'BAM' => 'KM',
            'BWP' => 'P',
            'BGN' => 'лв',
            'BRL' => 'R$',
            'BND' => '$',
            'KHR' => '៛',
            'CAD' => '$',
            'KYD' => '$',
            'CLP' => '$',
            'CNY' => '¥',
            'COP' => '$',
            'CRC' => '₡',
            'HRK' => 'kn',
            'CUP' => '₱',
            'CZK' => 'Kč',
            'DKK' => 'kr',
            'DOP' => 'RD$',
            'XCD' => '$',
            'EGP' => '£',
            'SVC' => '$',
            'EEK' => 'kr',
            'EUR' => '€',
            'FKP' => '£',
            'FJD' => '$',
            'GHC' => '¢',
            'GIP' => '£',
            'GTQ' => 'Q',
            'GGP' => '£',
            'GYD' => '$',
            'HNL' => 'L',
            'HKD' => '$',
            'HUF' => 'Ft',
            'ISK' => 'kr',
            'INR' => '₹',
            'IDR' => 'Rp',
            'IRR' => '﷼',
            'IMP' => '£',
            'ILS' => '₪',
            'JMD' => 'J$',
            'JPY' => '¥',
            'JEP' => '£',
            'KZT' => 'лв',
            'KPW' => '₩',
            'KRW' => '₩',
            'KGS' => 'лв',
            'LAK' => '₭',
            'LVL' => 'Ls',
            'LBP' => '£',
            'LRD' => '$',
            'LTL' => 'Lt',
            'MKD' => 'ден',
            'MYR' => 'RM',
            'MUR' => '₨',
            'MXN' => '$',
            'MNT' => '₮',
            'MZN' => 'MT',
            'NAD' => 'N$',
            'NPR' => '₨',
            'ANG' => 'ƒ',
            'NZD' => '$',
            'NIO' => 'C$',
            'NGN' => '₦',
            'KPW' => '₩',
            'NOK' => 'kr',
            'OMR' => '﷼',
            'PKR' => '₨',
            'PAB' => 'B/.',
            'PYG' => 'Gs',
            'PEN' => 'S/.',
            'PHP' => '₱',
            'PLN' => 'zł',
            'QAR' => '﷼',
            'RON' => 'lei',
            'RUB' => 'руб',
            'SHP' => '£',
            'SAR' => '﷼',
            'RSD' => 'Дин.',
            'SCR' => '₨',
            'SGD' => '$',
            'SBD' => '$',
            'SOS' => 'S',
            'ZAR' => 'R',
            'KRW' => '₩',
            'LKR' => '₨',
            'SEK' => 'kr',
            'CHF' => 'CHF',
            'SRD' => '$',
            'SYP' => '£',
            'TWD' => 'NT$',
            'THB' => '฿',
            'TTD' => 'TT$',
            'TRY' => 'TL',
            'TRL' => '₤',
            'TVD' => '$',
            'UAH' => '₴',
            'GBP' => '£',
            'USD' => '$',
            'UYU' => '$U',
            'UZS' => 'лв',
            'VEF' => 'Bs',
            'VND' => '₫',
            'YER' => '﷼',
            'ZWD' => 'Z$',
        );

        // Try to use order currency, or use default currency instead
        $order_currency_code = RightPress_WC_Legacy::order_get_currency($this->orderData);
        $currency_code = !empty($order_currency_code) ? $order_currency_code : get_woocommerce_currency();

        if ($symbol) {
            if (isset($currencies_symbols_list[$currency_code])) {
                return $currencies_symbols_list[$currency_code];
            }
            else {
                return false;
            }
        }
        else {
            return $currency_code;
        }
    }

    /**
     * Get currency name
     *
     * @param int $number
     * @return string
     */
    public function get_currency_name($number)
    {
        $dictionary  = array(
            'AED' => _n('dirham', 'dirhams', $number, 'woo_pdf'),
            'ARS' => _n('peso', 'pesos', $number, 'woo_pdf'),
            'AUD' => _n('dollar', 'dollars', $number, 'woo_pdf'),
            'BDT' => _n('taka', 'taka', $number, 'woo_pdf'),
            'BRL' => _n('real', 'reais', $number, 'woo_pdf'),
            'BGN' => _n('lev', 'leva', $number, 'woo_pdf'),
            'CAD' => _n('dollar', 'dollars', $number, 'woo_pdf'),
            'CLP' => _n('peso', 'pesos', $number, 'woo_pdf'),
            'CNY' => _n('yuan', 'yuan', $number, 'woo_pdf'),
            'COP' => _n('peso', 'pesos', $number, 'woo_pdf'),
            'CZK' => _n('koruna', 'koruna', $number, 'woo_pdf'),
            'DKK' => _n('krone', 'kroner', $number, 'woo_pdf'),
            'DOP' => _n('peso', 'pesos', $number, 'woo_pdf'),
            'EUR' => _n('euro', 'euros', $number, 'woo_pdf'),
            'HKD' => _n('dollar', 'dollars', $number, 'woo_pdf'),
            'HRK' => _n('kuna', 'kuna', $number, 'woo_pdf'),
            'HUF' => _n('forint', 'forint', $number, 'woo_pdf'),
            'ISK' => _n('krona', 'kronur', $number, 'woo_pdf'),
            'IDR' => _n('rupiah', 'rupiahs', $number, 'woo_pdf'),
            'INR' => _n('rupee', 'rupees', $number, 'woo_pdf'),
            'NPR' => _n('rupee', 'rupees', $number, 'woo_pdf'),
            'ILS' => _n('shekel', 'shekalim', $number, 'woo_pdf'),
            'JPY' => _n('yen', 'yen', $number, 'woo_pdf'),
            'KIP' => _n('kip', 'kip', $number, 'woo_pdf'),
            'KRW' => _n('won', 'won', $number, 'woo_pdf'),
            'MYR' => _n('ringgit', 'ringgit', $number, 'woo_pdf'),
            'MXN' => _n('peso', 'pesos', $number, 'woo_pdf'),
            'NGN' => _n('naira', 'naira', $number, 'woo_pdf'),
            'NOK' => _n('krone', 'kroner', $number, 'woo_pdf'),
            'NZD' => _n('dollar', 'dollars', $number, 'woo_pdf'),
            'PYG' => _n('guarani', 'guaranies', $number, 'woo_pdf'),
            'PHP' => _n('peso', 'pesos', $number, 'woo_pdf'),
            'PLN' => _n('zloty', 'zloty', $number, 'woo_pdf'),
            'GBP' => _n('pound', 'pounds', $number, 'woo_pdf'),
            'RON' => _n('leu', 'lei', $number, 'woo_pdf'),
            'RUB' => _n('ruble', 'rublei', $number, 'woo_pdf'),
            'SGD' => _n('dollar', 'dollars', $number, 'woo_pdf'),
            'ZAR' => _n('rand', 'rand', $number, 'woo_pdf'),
            'SEK' => _n('krona', 'kronor', $number, 'woo_pdf'),
            'CHF' => _n('franc', 'francs', $number, 'woo_pdf'),
            'TWD' => _n('dollar', 'dollars', $number, 'woo_pdf'),
            'THB' => _n('baht', 'baht', $number, 'woo_pdf'),
            'TRY' => _n('lira', 'liras', $number, 'woo_pdf'),
            'UAH' => _n('hryvnia', 'hryvni', $number, 'woo_pdf'),
            'USD' => _n('dollar', 'dollars', $number, 'woo_pdf'),
            'VND' => _n('dong', 'dong', $number, 'woo_pdf'),
            'EGP' => _n('pound', 'pounds', $number, 'woo_pdf')
        );

        // Try to use order currency, or use default currency instead
        $order_currency_code = RightPress_WC_Legacy::order_get_currency($this->orderData);
        $currency_code = !empty($order_currency_code) ? $order_currency_code : get_woocommerce_currency();

        if (isset($dictionary[$currency_code])) {
            return $dictionary[$currency_code];
        }

        return false;
    }

    /**
     * Get currency fraction name
     *
     * @param int $number
     * @return string
     */
    public function get_currency_fraction_name($number)
    {
        $dictionary  = array(
            'AED' => _n('fils', 'fulūs', $number, 'woo_pdf'),
            'ARS' => _n('centavo', 'centavos', $number, 'woo_pdf'),
            'AUD' => _n('cent', 'cents', $number, 'woo_pdf'),
            'BDT' => _n('paisa', 'paisa', $number, 'woo_pdf'),
            'BRL' => _n('centavo', 'centavos', $number, 'woo_pdf'),
            'BGN' => _n('stotinka', 'stotinki', $number, 'woo_pdf'),
            'CAD' => _n('cent', 'cents', $number, 'woo_pdf'),
            'CLP' => _n('centavo', 'centavos', $number, 'woo_pdf'),
            'CNY' => _n('fen', 'fen', $number, 'woo_pdf'),
            'COP' => _n('centavo', 'centavos', $number, 'woo_pdf'),
            'CZK' => _n('heller', 'heller', $number, 'woo_pdf'),
            'DKK' => _n('øre', 'øre', $number, 'woo_pdf'),
            'DOP' => _n('centavo', 'centavos', $number, 'woo_pdf'),
            'EUR' => _n('cent', 'cents', $number, 'woo_pdf'),
            'HKD' => _n('cent', 'cents', $number, 'woo_pdf'),
            'HRK' => _n('lipa', 'lipa', $number, 'woo_pdf'),
            'HUF' => _n('fillér', 'fillér', $number, 'woo_pdf'),
            'ISK' => _n('eyrir', 'aurar', $number, 'woo_pdf'),
            'IDR' => _n('sen', 'sen', $number, 'woo_pdf'),
            'INR' => _n('paisa ', 'paisa', $number, 'woo_pdf'),
            'NPR' => _n('paisa ', 'paisa', $number, 'woo_pdf'),
            'ILS' => _n('agora ', 'agorot', $number, 'woo_pdf'),
            'JPY' => _n('sen', 'sen', $number, 'woo_pdf'),
            'KIP' => _n('att', 'att', $number, 'woo_pdf'),
            'KRW' => _n('jeon', 'jeon', $number, 'woo_pdf'),
            'MYR' => _n('sen', 'sen', $number, 'woo_pdf'),
            'MXN' => _n('centavo', 'centavos', $number, 'woo_pdf'),
            'NGN' => _n('kobo', 'kobo', $number, 'woo_pdf'),
            'NOK' => _n('øre', 'øre', $number, 'woo_pdf'),
            'NZD' => _n('cent', 'cents', $number, 'woo_pdf'),
            'PYG' => _n('céntimo', 'céntimos', $number, 'woo_pdf'),
            'PHP' => _n('centavo', 'centavos', $number, 'woo_pdf'),
            'PLN' => _n('grosz', 'grosz', $number, 'woo_pdf'),
            'GBP' => _n('penny ', 'pennies', $number, 'woo_pdf'),
            'RON' => _n('ban', 'bani', $number, 'woo_pdf'),
            'RUB' => _n('kopeyka', 'kopeyek', $number, 'woo_pdf'),
            'SGD' => _n('cent', 'cents', $number, 'woo_pdf'),
            'ZAR' => _n('cent', 'cents', $number, 'woo_pdf'),
            'SEK' => _n('öre', 'öre', $number, 'woo_pdf'),
            'CHF' => _n('rappen', 'rappen', $number, 'woo_pdf'),
            'TWD' => _n('cent', 'cents', $number, 'woo_pdf'),
            'THB' => _n('satang', 'satang', $number, 'woo_pdf'),
            'TRY' => _n('kuruş', 'kuruş', $number, 'woo_pdf'),
            'UAH' => _n('kopiyka', 'kopiyky', $number, 'woo_pdf'),
            'USD' => _n('cent', 'cents', $number, 'woo_pdf'),
            'VND' => _n('hào', 'hào', $number, 'woo_pdf'),
            'EGP' => _n('piastre', 'piastres', $number, 'woo_pdf')
        );

        // Try to use order currency, or use default currency instead
        $order_currency_code = RightPress_WC_Legacy::order_get_currency($this->orderData);
        $currency_code = !empty($order_currency_code) ? $order_currency_code : get_woocommerce_currency();

        if (isset($dictionary[$currency_code])) {
            return $dictionary[$currency_code];
        }

        return false;
    }

    /**
     * Return capitalized amount in words
     *
     * @param int $number
     * @return string
     */
    public function get_amount_in_words($number)
    {
        return ucfirst($this->amount_in_words($number));
    }

    /**
     * Convert amount to amount in words
     * Based on: http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
     *
     * @param int $number
     * @param bool $self
     * @return string
     */
    public function amount_in_words($number, $self = false)
    {
        $hyphens = array('0' => '-', '1' => ' ', '2' => '');
        $hyphen = _x('-', 'separator for numbers 21 through 99 (amount in words)', 'woo_pdf');

        $conjunction = ' ' . __('and', 'woo_pdf') . ' ';

        $dictionary  = array(
            0                   => __('zero', 'woo_pdf'),
            1                   => __('one', 'woo_pdf'),
            2                   => __('two', 'woo_pdf'),
            3                   => __('three', 'woo_pdf'),
            4                   => __('four', 'woo_pdf'),
            5                   => __('five', 'woo_pdf'),
            6                   => __('six', 'woo_pdf'),
            7                   => __('seven', 'woo_pdf'),
            8                   => __('eight', 'woo_pdf'),
            9                   => __('nine', 'woo_pdf'),
            10                  => __('ten', 'woo_pdf'),
            11                  => __('eleven', 'woo_pdf'),
            12                  => __('twelve', 'woo_pdf'),
            13                  => __('thirteen', 'woo_pdf'),
            14                  => __('fourteen', 'woo_pdf'),
            15                  => __('fifteen', 'woo_pdf'),
            16                  => __('sixteen', 'woo_pdf'),
            17                  => __('seventeen', 'woo_pdf'),
            18                  => __('eighteen', 'woo_pdf'),
            19                  => __('nineteen', 'woo_pdf'),
            20                  => __('twenty', 'woo_pdf'),
            30                  => __('thirty', 'woo_pdf'),
            40                  => __('fourty', 'woo_pdf'),
            50                  => __('fifty', 'woo_pdf'),
            60                  => __('sixty', 'woo_pdf'),
            70                  => __('seventy', 'woo_pdf'),
            80                  => __('eighty', 'woo_pdf'),
            90                  => __('ninety', 'woo_pdf'),
        );

        if (!is_numeric($number)) {
            return $self ? $dictionary[0] : false;
        }
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 || ($self && empty($number))) {
            return $self ? $dictionary[0] : false;
        }

        // Make sure we have at least two digits for cents
        if (!$self) {
            $number = number_format((float)$number, absint(get_option('woocommerce_price_num_decimals')), '.', '');
        }

        $string = $fraction = null;

        // Accept both decimal separators
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
        else if (strpos($number, ',') !== false) {
            list($number, $fraction) = explode(',', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . _n('hundred', 'hundred', (int) $hundreds, 'woo_pdf');
                if ($remainder) {
                    $string .= ' ' . $this->amount_in_words($remainder, true);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;

                // Get translated string
                if ($baseUnit == 1000) {
                    $base_unit_translated = _n('thousand', 'thousand', $numBaseUnits, 'woo_pdf');
                }
                else if ($baseUnit == 1000000) {
                    $base_unit_translated = _n('million', 'million', $numBaseUnits, 'woo_pdf');
                }
                else if ($baseUnit == 1000000000) {
                    $base_unit_translated = _n('billion', 'billion', $numBaseUnits, 'woo_pdf');
                }
                else if ($baseUnit == 1000000000000) {
                    $base_unit_translated = _n('trillion', 'trillion', $numBaseUnits, 'woo_pdf');
                }
                else if ($baseUnit == 1000000000000000) {
                    $base_unit_translated = _n('quadrillion', 'quadrillion', $numBaseUnits, 'woo_pdf');
                }
                else if ($baseUnit == 1000000000000000000) {
                    $base_unit_translated = _n('quintillion', 'quintillion', $numBaseUnits, 'woo_pdf');
                }

                $string = $this->amount_in_words($numBaseUnits, true) . ' ' . $base_unit_translated;
                if ($remainder) {
                    //$string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= ' ' . $this->amount_in_words($remainder, true);
                }
                break;
        }

        if (!$self) {
            $string .= ' ';
            //$string .= _n('dollar', 'dollars', $number, 'woo_pdf');
            $string .= $this->get_currency_name($number);
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $string .= $conjunction;
            $string .= $this->amount_in_words(ltrim($fraction, '0'), true);
            //$string .= $fraction;
            $string .= ' ';
            //$string .= _n('cent', 'cents', $fraction, 'woo_pdf');
            $string .= $this->get_currency_fraction_name($fraction);
        }

        return $string;
    }

    /**
     * Get product categories
     *
     * @access public
     * @param array $product_id
     * @return mixed
     */
    public function get_product_category_names($product_id)
    {
        $categories = array();
        $current_categories = wp_get_post_terms($product_id, 'product_cat');

        $post_categories_raw = null;

        foreach ($current_categories as $category) {
            $category_name = $category->name;

            // WC31: Check if categories are still post terms
            if ($category->parent) {

                if (is_null($post_categories_raw)) {
                    $post_categories_raw = get_terms(array('product_cat'), array('hide_empty' => 0));
                }

                $parent_id = $category->parent;
                $has_parent = true;

                while ($has_parent) {
                    foreach ($post_categories_raw as $parent_post_cat_key => $parent_post_cat) {
                        if ($parent_post_cat->term_id == $parent_id) {
                            $category_name = $parent_post_cat->name . ' &rarr; ' . $category_name;

                            if ($parent_post_cat->parent) {
                                $parent_id = $parent_post_cat->parent;
                            }
                            else {
                                $has_parent = false;
                            }

                            break;
                        }
                    }
                }
            }

            $categories[$category->term_id] = $category_name;
        }

        return !empty($categories) ? $categories : false;
    }

    /**
     * Get correctly rounded and formatted price with enough decimals
     *
     * @access public
     * @param float $price
     * @return string
     */
    public function get_rounded_price($price, $quantity)
    {
        // Get absolute maximum decimal number
        $min_decimals = get_option('woocommerce_price_num_decimals');
        $max_decimals = $min_decimals + strlen((string) $quantity);

        // Initial decimals check
        $actual_decimals = strlen(substr(strrchr($price, '.'), 1));
        $price_decimals = $actual_decimals > $max_decimals ? $max_decimals : $actual_decimals;

        // Trim price to the specific amount of decimals and remove trailing zeros
        $factor = (float) '1' . str_repeat('0', $price_decimals);
        $price = round((float) $price * $factor) / $factor;

        // Second decimals check (in case we don't need all the decimal places because of trailing zeros that got removed)
        $actual_decimals = strlen(substr(strrchr($price, '.'), 1));
        $price_decimals = $actual_decimals < $min_decimals ? $min_decimals : $actual_decimals;

        // Format and return
        return $this->format_currency($price, $price_decimals);
    }


}

}

?>