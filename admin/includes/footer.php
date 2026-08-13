<?php /** Portal footer: closes the layout and loads the shared scripts. */ ?>
        </div><!-- /.admin-content -->
    </div><!-- /.admin-main -->
</div><!-- /.admin-shell -->

<!-- Media picker, shared by every image field -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose a picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    <input type="search" class="form-control" id="mediaPickerSearch" placeholder="Filter by file name…" style="max-width:320px;">
                    <select class="form-select" id="mediaPickerFolder" style="max-width:250px;">
                        <option value="">All folders</option>
                    </select>
                    <span class="ms-auto align-self-center form-hint" id="mediaPickerCount"></span>
                </div>
                <div id="mediaPickerGrid"></div>
            </div>
        </div>
    </div>
</div>

<script src="<?= url('assets/js/bootstrap.min.js') ?>"></script>
<script>
(function () {
    // Sidebar slide-in on small screens, with a backdrop to close it again.
    var toggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('adminSidebar');
    if (toggle && sidebar) {
        var backdrop = null;

        function closeSidebar() {
            sidebar.classList.remove('open');
            if (backdrop) { backdrop.remove(); backdrop = null; }
        }

        toggle.addEventListener('click', function () {
            if (sidebar.classList.contains('open')) { return closeSidebar(); }
            sidebar.classList.add('open');
            backdrop = document.createElement('div');
            backdrop.className = 'sidebar-backdrop';
            backdrop.addEventListener('click', closeSidebar);
            document.body.appendChild(backdrop);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') { closeSidebar(); }
        });
    }

    // ---- Media picker -------------------------------------------------
    var modalEl = document.getElementById('mediaPickerModal');
    if (!modalEl) { return; }

    var modal = new bootstrap.Modal(modalEl);
    var grid = document.getElementById('mediaPickerGrid');
    var search = document.getElementById('mediaPickerSearch');
    var folderSelect = document.getElementById('mediaPickerFolder');
    var countEl = document.getElementById('mediaPickerCount');
    var targetInput = null;
    var library = null;

    function render() {
        if (!library) { return; }
        var term = search.value.toLowerCase();
        var folder = folderSelect.value;
        var items = library.filter(function (item) {
            if (folder && item.folder !== folder) { return false; }
            return !term || item.path.toLowerCase().indexOf(term) !== -1;
        }).slice(0, 400);

        grid.innerHTML = '';
        items.forEach(function (item) {
            var button = document.createElement('button');
            button.type = 'button';
            button.title = item.path;
            button.innerHTML = '<img loading="lazy" src="' + item.thumb + '" alt="">'
                + '<div class="name">' + item.name + '</div>';
            button.addEventListener('click', function () {
                if (targetInput) {
                    targetInput.value = item.path;
                    targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                modal.hide();
            });
            grid.appendChild(button);
        });
        countEl.textContent = items.length + ' of ' + library.length + ' pictures';
    }

    function loadLibrary() {
        if (library) { render(); return; }
        grid.innerHTML = '<p class="form-hint">Loading the picture library…</p>';
        fetch('<?= url('admin/media.php') ?>?json=1')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                library = data.items || [];
                (data.folders || []).forEach(function (folder) {
                    var option = document.createElement('option');
                    option.value = folder;
                    option.textContent = folder;
                    folderSelect.appendChild(option);
                });
                render();
            })
            .catch(function () {
                grid.innerHTML = '<p class="text-danger">Could not load the picture library.</p>';
            });
    }

    search.addEventListener('input', render);
    folderSelect.addEventListener('change', render);

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-media-picker]');
        if (!trigger) { return; }
        event.preventDefault();
        targetInput = document.getElementById(trigger.getAttribute('data-media-picker'));
        modal.show();
        loadLibrary();
    });

    // Live preview when an image path changes
    document.querySelectorAll('input[data-image-input]').forEach(function (input) {
        input.addEventListener('change', function () {
            var preview = document.querySelector('img[data-preview-for="' + input.id + '"]');
            if (!preview) { return; }
            preview.src = input.value
                ? '<?= base_url('thumb.php') ?>?w=300&f=' + encodeURIComponent(input.value)
                : '<?= url('assets/images/placeholder.svg') ?>';
        });
    });

    // Auto-fill slug fields from their source field
    document.querySelectorAll('input[data-slug-from]').forEach(function (slugInput) {
        var source = document.querySelector('[name="' + slugInput.getAttribute('data-slug-from') + '"]');
        if (!source) { return; }
        source.addEventListener('blur', function () {
            if (slugInput.value.trim() !== '') { return; }
            slugInput.value = source.value.toLowerCase()
                .replace(/[‘’']/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .slice(0, 120);
        });
    });

    // Confirm destructive actions
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });
})();
</script>
</body>
</html>
