<?php
/**
 * Form field rendering and reading, driven by the entity definitions.
 */

declare(strict_types=1);

/** Resolve the choices of a select field. */
function field_options(array $field): array
{
    if (!empty($field['options'])) {
        return $field['options'];
    }
    if (!empty($field['options_sql'])) {
        $options = [];
        foreach (fetch_all($field['options_sql']) as $row) {
            $values = array_values($row);
            $options[(string) $values[0]] = (string) ($values[1] ?? $values[0]);
        }
        return $options;
    }
    return [];
}

/** The value a new record starts with. */
function field_default(array $field)
{
    $default = $field['default'] ?? '';
    if ($field['type'] === 'date' && $default === 'today') {
        return date('Y-m-d');
    }
    return $default;
}

/** Render one form field. */
function render_field(string $name, array $field, $value): string
{
    $id      = 'f_' . $name;
    $label   = $field['label'] ?? ucfirst(str_replace('_', ' ', $name));
    $req     = !empty($field['required']);
    $hint    = $field['hint'] ?? '';
    $ro      = !empty($field['readonly']) ? ' readonly' : '';
    $col     = (int) ($field['col'] ?? 12);
    $rows    = (int) ($field['rows'] ?? 4);
    $value   = $value ?? '';

    ob_start();
    echo '<div class="col-md-' . $col . '"><div class="field-block">';

    if ($field['type'] !== 'checkbox') {
        echo '<label class="form-label" for="' . h($id) . '">' . h($label)
            . ($req ? ' <span style="color:#d9534f">*</span>' : '') . '</label>';
    }

    switch ($field['type']) {
        case 'textarea':
        case 'html':
            echo '<textarea class="form-control" id="' . h($id) . '" name="' . h($name) . '" rows="' . $rows . '"'
                . ($req ? ' required' : '') . $ro . '>' . h($value) . '</textarea>';
            if ($field['type'] === 'html') {
                $hint = $hint ?: 'Basic HTML is allowed: &lt;p&gt; &lt;h4&gt; &lt;ul&gt; &lt;li&gt; &lt;strong&gt; &lt;em&gt; &lt;a&gt;.';
            }
            break;

        case 'select':
            echo '<select class="form-select" id="' . h($id) . '" name="' . h($name) . '"' . ($req ? ' required' : '') . '>';
            if (isset($field['empty']) || !$req) {
                echo '<option value="">' . h($field['empty'] ?? '— none —') . '</option>';
            }
            foreach (field_options($field) as $key => $text) {
                $selected = ((string) $value === (string) $key) ? ' selected' : '';
                echo '<option value="' . h($key) . '"' . $selected . '>' . h($text) . '</option>';
            }
            echo '</select>';
            break;

        case 'checkbox':
            echo '<div class="form-check mt-4">'
                . '<input type="hidden" name="' . h($name) . '" value="0">'
                . '<input class="form-check-input" type="checkbox" id="' . h($id) . '" name="' . h($name) . '" value="1"'
                . ((int) $value === 1 ? ' checked' : '') . '>'
                . '<label class="form-check-label" for="' . h($id) . '">' . h($label) . '</label>'
                . '</div>';
            break;

        case 'number':
            echo '<input type="number" class="form-control" id="' . h($id) . '" name="' . h($name) . '" value="'
                . h($value) . '"' . ($req ? ' required' : '') . $ro . '>';
            break;

        case 'date':
            echo '<input type="date" class="form-control" id="' . h($id) . '" name="' . h($name) . '" value="'
                . h($value) . '"' . ($req ? ' required' : '') . $ro . '>';
            break;

        case 'email':
            echo '<input type="email" class="form-control" id="' . h($id) . '" name="' . h($name) . '" value="'
                . h($value) . '"' . ($req ? ' required' : '') . $ro . '>';
            break;

        case 'url':
            echo '<input type="url" class="form-control" id="' . h($id) . '" name="' . h($name) . '" value="'
                . h($value) . '" placeholder="https://…"' . $ro . '>';
            break;

        case 'slug':
            echo '<input type="text" class="form-control" id="' . h($id) . '" name="' . h($name) . '" value="'
                . h($value) . '" data-slug-from="' . h($field['from'] ?? '') . '"' . $ro . '>';
            $hint = $hint ?: 'Leave empty and it will be generated automatically.';
            break;

        case 'image':
            $preview = $value !== '' ? img((string) $value, 300) : url('assets/images/placeholder.svg');
            echo '<div class="image-field">'
                . '<img class="preview" src="' . h($preview) . '" alt="" data-preview-for="' . h($id) . '">'
                . '<div class="controls">'
                . '<input type="text" class="form-control mb-2" id="' . h($id) . '" name="' . h($name) . '" value="'
                . h($value) . '" placeholder="photos/… or uploads/…" data-image-input>'
                . '<div class="d-flex gap-2 flex-wrap align-items-center">'
                . '<button type="button" class="btn btn-sm btn-outline-cmsr" data-media-picker="' . h($id) . '">'
                . '<i class="bi-collection me-1"></i>Choose from library</button>'
                . '<input type="file" class="form-control form-control-sm" name="' . h($name) . '__upload" accept="image/*" style="max-width:270px;">'
                . '</div></div></div>';
            $hint = $hint ?: 'Pick a picture from the library, or upload a new one — the upload wins.';
            break;

        case 'file':
            echo '<input type="text" class="form-control mb-2" id="' . h($id) . '" name="' . h($name) . '" value="'
                . h($value) . '" placeholder="uploads/files/…">';
            echo '<input type="file" class="form-control form-control-sm" name="' . h($name) . '__upload">';
            if ($value !== '') {
                echo '<div class="form-hint mt-1"><a href="' . h(url((string) $value)) . '" target="_blank">'
                    . '<i class="bi-download me-1"></i>' . h(basename((string) $value)) . '</a></div>';
            }
            break;

        default: // text
            echo '<input type="text" class="form-control" id="' . h($id) . '" name="' . h($name) . '" value="'
                . h($value) . '"' . ($req ? ' required' : '') . $ro . '>';
    }

    if ($hint !== '') {
        echo '<div class="form-hint">' . $hint . '</div>';
    }

    echo '</div></div>';
    return (string) ob_get_clean();
}

/**
 * Read every field of an entity out of the request.
 *
 * @return array{0: array<string,mixed>, 1: string[]} [values, errors]
 */
function read_fields(array $fields, array $existing = []): array
{
    $values = [];
    $errors = [];

    foreach ($fields as $name => $field) {
        if (!empty($field['readonly']) && $existing) {
            continue; // never let a read-only value be rewritten
        }

        switch ($field['type']) {
            case 'checkbox':
                $values[$name] = (int) post($name, 0) === 1 ? 1 : 0;
                break;

            case 'number':
                $values[$name] = (int) post($name, 0);
                break;

            case 'image':
            case 'file':
                $current = (string) post($name, $existing[$name] ?? '');
                try {
                    $uploaded = handle_upload(
                        $name . '__upload',
                        $field['type'] === 'image' ? 'media' : 'files',
                        $field['type'] === 'image'
                    );
                    if ($uploaded !== null) {
                        $current = $uploaded;
                        if ($field['type'] === 'image') {
                            db_insert('media', [
                                'title'       => basename($uploaded),
                                'file_path'   => $uploaded,
                                'folder'      => 'uploads',
                                'mime_type'   => (string) (mime_content_type(ROOT_PATH . '/' . $uploaded) ?: ''),
                                'file_size'   => (int) filesize(ROOT_PATH . '/' . $uploaded),
                                'uploaded_by' => current_user()['id'] ?? null,
                            ]);
                        }
                    }
                } catch (RuntimeException $e) {
                    $errors[] = ($field['label'] ?? $name) . ': ' . $e->getMessage();
                }
                $values[$name] = $current;
                break;

            case 'html':
                $values[$name] = safe_html((string) post($name));
                break;

            case 'date':
                $raw = (string) post($name);
                $values[$name] = $raw !== '' ? $raw : null;
                break;

            case 'select':
                $raw = (string) post($name);
                // Nullable foreign keys must be NULL, not ''.
                $values[$name] = ($raw === '' && str_ends_with($name, '_id')) ? null : $raw;
                break;

            default:
                $values[$name] = (string) post($name);
        }

        if (!empty($field['required'])) {
            $given = $values[$name] ?? '';
            if ($given === '' || $given === null) {
                $errors[] = ($field['label'] ?? $name) . ' is required.';
            }
        }
        if ($field['type'] === 'email' && !empty($values[$name]) && !filter_var($values[$name], FILTER_VALIDATE_EMAIL)) {
            $errors[] = ($field['label'] ?? $name) . ' must be a valid e-mail address.';
        }
    }

    return [$values, $errors];
}
