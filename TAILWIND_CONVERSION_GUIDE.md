# Panduan Konversi Bootstrap ke Tailwind CSS

## Konversi Kelas Bootstrap → Tailwind

### Layout & Grid
| Bootstrap | Tailwind |
|-----------|----------|
| `container` | `w-full max-w-7xl mx-auto` |
| `container-fluid` | `w-full px-4` |
| `row` | `flex flex-wrap -mx-2` |
| `col-12` | `w-full px-2` |
| `col-6` | `w-full sm:w-1/2 px-2` |
| `col-4` | `w-full sm:w-1/3 px-2` |
| `col-3` | `w-full sm:w-1/4 px-2` |

### Display & Flexbox
| Bootstrap | Tailwind |
|-----------|----------|
| `d-flex` | `flex` |
| `align-items-center` | `items-center` |
| `align-items-start` | `items-start` |
| `align-items-end` | `items-end` |
| `justify-content-center` | `justify-center` |
| `justify-content-between` | `justify-between` |
| `justify-content-end` | `justify-end` |

### Spacing
| Bootstrap | Tailwind |
|-----------|----------|
| `m-0` to `m-5` | `m-0` to `m-20` |
| `p-0` to `p-5` | `p-0` to `p-20` |
| `mb-4` | `mb-4` |
| `mt-2` | `mt-2` |

### Typography
| Bootstrap | Tailwind |
|-----------|----------|
| `font-weight-bold` | `font-bold` |
| `font-weight-normal` | `font-normal` |
| `text-center` | `text-center` |
| `text-right` | `text-right` |
| `text-muted` | `text-gray-500` |
| `text-primary` | `text-blue-600` |
| `text-danger` | `text-red-600` |
| `text-success` | `text-green-600` |
| `text-warning` | `text-yellow-600` |

### Cards
```html
<!-- Bootstrap -->
<div class="card">
    <div class="card-header">
        <h5>Title</h5>
    </div>
    <div class="card-body">
        Content
    </div>
</div>

<!-- Tailwind -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 px-6 py-4">
        <h5 class="font-bold text-gray-800">Title</h5>
    </div>
    <div class="p-6">
        Content
    </div>
</div>
```

### Buttons
```html
<!-- Bootstrap -->
<button class="btn btn-primary">Click</button>
<button class="btn btn-sm btn-secondary">Small</button>

<!-- Tailwind -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">Click</button>
<button class="px-2 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition duration-200">Small</button>
```

### Badges
```html
<!-- Bootstrap -->
<span class="badge badge-primary">Label</span>
<span class="badge badge-success">Success</span>

<!-- Tailwind -->
<span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Label</span>
<span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Success</span>
```

### Tables
```html
<!-- Bootstrap -->
<table class="table table-hover">
    <thead class="thead-light">
        <tr>
            <th>Header</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Cell</td>
        </tr>
    </tbody>
</table>

<!-- Tailwind -->
<table class="w-full border-collapse">
    <thead class="bg-gray-100 border-b-2 border-gray-300">
        <tr>
            <th class="px-6 py-3 text-left font-semibold text-gray-700">Header</th>
        </tr>
    </thead>
    <tbody>
        <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-200">
            <td class="px-6 py-3 text-gray-600">Cell</td>
        </tr>
    </tbody>
</table>
```

### Alerts
```html
<!-- Bootstrap -->
<div class="alert alert-success">Success message</div>
<div class="alert alert-danger">Error message</div>

<!-- Tailwind -->
<div class="p-4 bg-green-50 border-l-4 border-green-600 rounded text-green-700">Success message</div>
<div class="p-4 bg-red-50 border-l-4 border-red-600 rounded text-red-700">Error message</div>
```

### Forms
```html
<!-- Bootstrap -->
<input type="text" class="form-control" placeholder="Input">
<select class="form-control">
    <option>Option</option>
</select>

<!-- Tailwind -->
<input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Input">
<select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    <option>Option</option>
</select>
```

## Tips Konversi
1. Gunakan `@apply` directive di custom CSS untuk menciptakan reusable components
2. Selalu gunakan `transition` dan `duration-200` untuk hover effects
3. Gunakan `shadow-md` atau `shadow-lg` untuk depth
4. Gunakan `rounded-lg` untuk modern look
5. Gunakan gradient untuk headers: `bg-gradient-to-r from-blue-50 to-blue-100`
6. Gunakan `text-gray-600`, `text-gray-700`, `text-gray-800` untuk text bukan `text-muted`
7. Selalu mobile-first: `px-2 sm:px-4` untuk padding responsive

## Custom CSS Classes (Sudah Tersedia)
Gunakan class-class ini di HTML untuk konsistensi:
- `.card-tailwind` - Untuk card wrapper
- `.card-header-tailwind` - Untuk card header
- `.card-body-tailwind` - Untuk card body
- `.btn-primary-tailwind` - Untuk button primary
- `.btn-success-tailwind` - Untuk button success
- `.input-tailwind` - Untuk form input
- `.table-tailwind` - Untuk table
- `.alert-success-tailwind` - Untuk alert success
