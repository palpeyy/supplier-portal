# TAILWIND CONVERSION HELPER GUIDE

## Quick Reference: Bootstrap to Tailwind Mappings

### Layout & Grid
| Bootstrap | Tailwind | Notes |
|-----------|----------|-------|
| `.container-fluid` | `.w-full .max-w-full .px-4` | Add py-6 for padding |
| `.row` | `.grid .grid-cols-1` | Or use flex |
| `.col-md-6` | `.md:grid-cols-2` or `.md:w-1/2` | |
| `.col-sm-6` | `.sm:grid-cols-2` | |
| `.mb-2, .mb-4` | `.mb-2, .mb-4` | Same in Tailwind |
| `.mt-3` | `.mt-3` | Same in Tailwind |

### Cards
| Bootstrap | Tailwind |
|-----------|----------|
| `.card` | `.bg-white .rounded-lg .shadow-md .overflow-hidden` |
| `.card-header` | `.bg-gradient-to-r .from-blue-50 .to-blue-100 .border-b .border-blue-200 .px-6 .py-4` |
| `.card-title` | `.font-semibold .text-lg .text-gray-800` |
| `.card-body` | `.p-6` |
| `.card-footer` | `.border-t .border-gray-200 .px-6 .py-4` |

### Buttons
| Bootstrap | Tailwind |
|-----------|----------|
| `.btn.btn-primary` | `.bg-blue-600 .hover:bg-blue-700 .text-white .px-4 .py-2 .rounded-lg .transition .duration-200` |
| `.btn.btn-warning` | `.bg-yellow-500 .hover:bg-yellow-600 .text-white` |
| `.btn.btn-danger` | `.bg-red-600 .hover:bg-red-700 .text-white` |
| `.btn.btn-secondary` | `.bg-gray-500 .hover:bg-gray-600 .text-white` |
| `.btn-sm` | `.px-2 .py-1 .text-xs` |
| `.btn-lg` | `.px-6 .py-3 .text-lg` |

### Tables
| Bootstrap | Tailwind |
|-----------|----------|
| `.table` | `.w-full .border-collapse` |
| `<thead>` | `.bg-gray-100 .border-b .border-gray-200` |
| `<th>` | `.px-6 .py-3 .text-left .text-sm .font-semibold .text-gray-700` |
| `<tbody>` | (no class needed) |
| `<tr>` | `.border-b .border-gray-200 .hover:bg-gray-50 .transition .duration-200` |
| `<td>` | `.px-6 .py-3 .text-sm .text-gray-600` |
| `.table-hover` | (handled by tr hover class) |

### Badges
| Bootstrap | Tailwind |
|-----------|----------|
| `.badge.badge-success` | `.inline-block .bg-green-600 .text-white .px-3 .py-1 .rounded-full .text-xs .font-semibold` |
| `.badge.badge-warning` | `.inline-block .bg-yellow-600 .text-white ...` |
| `.badge.badge-danger` | `.inline-block .bg-red-600 .text-white ...` |
| `.badge.badge-info` | `.inline-block .bg-blue-600 .text-white ...` |

### Forms
| Bootstrap | Tailwind |
|-----------|----------|
| `.form-group` | `<div>` (no class) |
| `.form-control` | `.w-full .px-4 .py-2 .border .border-gray-300 .rounded-lg .focus:outline-none .focus:border-blue-600` |
| `.form-check` | `.flex .items-center` |
| `label` | `.block .text-gray-700 .font-semibold .mb-2` |
| `.text-danger` | `.text-red-600` |
| `.@error` highlight | `.border-red-500 .@enderror` |

### Alerts
| Bootstrap | Tailwind |
|-----------|----------|
| `.alert.alert-success` | `.p-4 .bg-green-50 .border .border-green-200 .rounded-lg` |
| Alert text | `.text-green-700 .font-semibold` |
| Close button | `.float-right .text-green-700 .hover:text-green-900 .font-bold` |
| `.alert-danger` | `.p-4 .bg-red-50 .border .border-red-200 .rounded-lg` |

### Modals
| Bootstrap | Tailwind |
|-----------|----------|
| `.modal.fade` | (keep Bootstrap for functionality) |
| `.modal-dialog` | (keep Bootstrap) |
| `.modal-content` | `.bg-white .rounded-lg .shadow-lg` |
| `.modal-header` | `.bg-blue-600 .text-white .px-6 .py-4 .rounded-t-lg` |
| `.modal-body` | `.px-6 .py-4` |
| `.modal-footer` | `.border-t .border-gray-200 .px-6 .py-4 .flex .gap-2 .justify-end` |
| `.close` button | `.absolute .right-4 .top-3 .text-white .hover:text-gray-200 .text-2xl` |

### Text & Typography
| Bootstrap | Tailwind |
|-----------|----------|
| `.text-center` | `.text-center` |
| `.text-right` | `.text-right` |
| `.font-weight-bold` | `.font-bold` |
| `.font-weight-light` | `.font-light` |
| `.text-muted` | `.text-gray-500` |
| `.text-danger` | `.text-red-600` |
| `.text-success` | `.text-green-600` |
| `<h1>` | `.text-3xl .font-bold .text-gray-800` |
| `<h3>` | `.text-lg .font-semibold .text-gray-800` |
| `<h5>` | `.font-semibold .text-lg` |

### Dropdowns
| Bootstrap | Tailwind |
|-----------|----------|
| `.dropdown` | (keep Bootstrap for JS) |
| `.btn-sm` links | `.inline-flex .items-center .px-2 .py-1 ...` |

### Breadcrumb
| Bootstrap | Tailwind |
|-----------|----------|
| `.breadcrumb` | `.flex .items-center .space-x-2 .text-sm` |
| `.breadcrumb-item` | `.flex .items-center` |
| `.active` | Remove class, just text |
| Links | `.text-blue-600 .hover:text-blue-800` |

## Common Patterns

### Button with Icon
```blade
<!-- BEFORE -->
<button class="btn btn-primary btn-sm">
    <i class="fas fa-plus"></i> Tambah
</button>

<!-- AFTER -->
<button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg transition duration-200">
    <i class="fas fa-plus mr-2"></i> Tambah
</button>
```

### Action Button Group
```blade
<!-- BEFORE -->
<td>
    <a class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i>
    </a>
    <button class="btn btn-danger btn-sm">
        <i class="fas fa-trash"></i>
    </button>
</td>

<!-- AFTER -->
<td class="flex gap-1">
    <a class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded transition duration-200">
        <i class="fas fa-edit"></i>
    </a>
    <button class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition duration-200">
        <i class="fas fa-trash"></i>
    </button>
</td>
```

### Modal Form
```blade
<!-- BEFORE -->
<div class="modal-header">
    <h5 class="modal-title">Title</h5>
    <button type="button" class="close">...</button>
</div>
<div class="modal-body">
    Form content
</div>
<div class="modal-footer">
    <button class="btn btn-secondary">Cancel</button>
    <button class="btn btn-primary">Save</button>
</div>

<!-- AFTER -->
<div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg border-b border-blue-700">
    <h5 class="font-semibold text-lg">Title</h5>
    <button type="button" class="absolute right-4 top-3 text-white hover:text-gray-200 text-2xl">...</button>
</div>
<div class="px-6 py-4">
    Form content
</div>
<div class="border-t border-gray-200 px-6 py-4 flex gap-2 justify-end">
    <button class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">Cancel</button>
    <button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Save</button>
</div>
```

## Spacing Reference

### Padding
- `.px-2` = 0.5rem (8px)
- `.px-4` = 1rem (16px)
- `.px-6` = 1.5rem (24px)
- `.py-2` = 0.5rem (8px)
- `.py-3` = 0.75rem (12px)
- `.py-4` = 1rem (16px)
- `.py-6` = 1.5rem (24px)

### Margins
- `.mb-2` = 0.5rem (8px)
- `.mb-4` = 1rem (16px)
- `.mb-6` = 1.5rem (24px)
- `.mt-2` = 0.5rem (8px)
- `.mt-3` = 0.75rem (12px)
- `.mt-6` = 1.5rem (24px)

### Gap (for flex/grid)
- `.gap-1` = 0.25rem (4px)
- `.gap-2` = 0.5rem (8px)
- `.gap-4` = 1rem (16px)

## Color Palette

### Primary
- `blue-600` - Main blue color
- `blue-700` - Hover state
- `blue-50` - Light background
- `blue-100` - Lighter background

### Success
- `green-600` - Success badge/button

### Warning/Edit
- `yellow-500` - Warning button
- `yellow-600` - Hover state

### Danger/Delete
- `red-600` - Danger button
- `red-700` - Hover state

### Neutral
- `gray-50` - Very light background
- `gray-100` - Light background
- `gray-300` - Border color
- `gray-500` - Secondary button
- `gray-600` - Hover state
- `gray-700` - Text
- `gray-800` - Dark text
- `white` - Pure white background

## Font Sizes

- `.text-xs` = 0.75rem (12px)
- `.text-sm` = 0.875rem (14px)
- `.text-base` = 1rem (16px)
- `.text-lg` = 1.125rem (18px)
- `.text-3xl` = 1.875rem (30px)

## Responsive Breakpoints

- `.sm:` = 640px and up
- `.md:` = 768px and up  
- `.lg:` = 1024px and up
- `.xl:` = 1280px and up

## Implementation Tips

1. **Always include `mr-2` or `mr-3` between icon and text**
2. **Use `.inline-flex .items-center` for button alignment**
3. **Add `.transition .duration-200` to all hover effects**
4. **Use `.transform .hover:-translate-y-0.5` for subtle lift effect**
5. **Always close tags with `rounded-t-lg` on modals**
6. **Use `.w-full` on form inputs**
7. **Remember `.outline-none` on focus for inputs**
8. **Use gradient headers: `.bg-gradient-to-r .from-color-50 .to-color-100`**

## Validation

After conversion, check:
- ✅ All colors applied correctly
- ✅ Spacing and padding consistent
- ✅ Buttons have proper hover states
- ✅ Tables have proper row striping
- ✅ Forms display correctly
- ✅ Modals have proper styling
- ✅ Responsive on mobile devices
- ✅ Icons have proper spacing
- ✅ Badges display correctly
- ✅ Transitions are smooth
