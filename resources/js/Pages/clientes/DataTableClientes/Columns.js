import { h } from 'vue'
import DropDownOrderActions from './DropDownOrderActions.vue'
import { ArrowUpDown } from 'lucide-vue-next'
import { utf8Decode } from '@/util'
import { Button } from '@/components/ui/button'
import {
    createColumnHelper,
} from '@tanstack/vue-table'
import DataTableNumberField from './DataTableNumberField.vue'
import TableCell from './TableCell.vue'
import { formatMoney } from '@/util';

const { toCurrency } = formatMoney()
/*
 {
    id: '728ed52f',
    amount: 100,
    status: 'pending',
    email: 'm@example.com',
  },

*/
const columnHelper = createColumnHelper()

const fuzzySort = (rowA, rowB, columnId) => {
    console.log(rowA)
    //get rowA aggregate rank
    const rowAAggregateRank = rowA.columnFiltersMeta[columnId]?.aggregateRank;

    //get rowB aggregate rank
    const rowBAggregateRank = rowB.columnFiltersMeta[columnId]?.aggregateRank;

    //calculate sort direction
    const dir =
        rowAAggregateRank < rowBAggregateRank
            ? -1
            : rowBAggregateRank < rowAAggregateRank
                ? 1
                : 0;

    // Provide an alphanumeric fallback for when the item ranks are equal (this is where the 'default column' value comes in handy)
    return dir === 0 ? sortingFns.alphanumeric(rowA, rowB, columnId) : dir;
};


export const columns = [
    // columnHelper.accessor((row) => row.id, {
    //     id: "globalSort",
    //     sortingFn: {
    //         myCustomSorting: fuzzySort
    //     },
    //     enableGlobalFilter: true,
    // }),
    {
        accessorKey: 'img',
        header: () => h('div', { class: 'text-white font-bold' }, 'img'),
        cell: ({ row }) => {
            // console.log(`(${row.original.dddTelefone}) ${row.getValue('telefone')}`)
            return h('img', { class: 'font-medium', src: row.getValue('img') },)
        },
        enableGlobalFilter: false,
    },
    {
        accessorKey: 'nome',
        accessorFn: ({ nome }) => utf8Decode(nome),
        header: ({ column }) => {
            return h(Button, {
                class: 'hover:!bg-transparent ring-transparent p-0 hover:!text-white text-sm font-bold text-white',
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['nome', h(ArrowUpDown, { class: 'hover:!bg-transparent ring-transparent p-0 hover:!text-white ml-2 h-4 w-4' })])
        },
        cell: ({ row }) => h('div', { class: '' }, utf8Decode(row.getValue('nome'))),
        enableGlobalFilter: false,
    },
    {
        accessorKey: 'preco',
        accessorFn: ({ preco }) => preco,
        header: ({ column }) => {
            return h(Button, {
                class: 'hover:!bg-transparent ring-transparent p-0 hover:!text-white text-sm font-medium font-bold text-white',
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['preco', h(ArrowUpDown, { class: 'hover:!bg-transparent ring-transparent p-0 hover:!text-white ml-2 h-4 w-4' })])
        },
        cell: ({ row, getValue, column, table, cell }) => {

            return h(TableCell, {
                cellvalue: getValue()[0].val,
                cellkey: cell.id,
                'onChanged': (val) => {
                    table.options.meta.updateData(row.index, column.id, val.value)
                }
            }, toCurrency(getValue()[0].val))
        },
        meta: {

        },
        enableGlobalFilter: false,
    },
    {
        id: 'quantidade',
        enableHiding: false,
        header: () => h('div', { class: 'font-bold text-white' }, 'quantidade'),
        cell: ({ row, getValue, column, table, cell }) => {
            const payment = row.original

            return h(DataTableNumberField, {
                payment,
                onExpand: row.toggleExpanded,
                'onUpdate:modelValue': (val) => {
                    table.options.meta.updateData(row.index, column.id, val)
                }
            })
        },
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const payment = row.original

            return h('div', { class: 'relative' }, h(DropDownOrderActions, {
                payment,
            }))
        },
    },
]

// export const columns = [
//     columnHelper.accessor('nome', {
//         cell: info => info.getValue(),
//     }),
//     columnHelper.accessor(row => row.telefone, {
//         id: 'telefone',
//         cell: info => info.getValue(),
//     }),
//     columnHelper.accessor(row => row.enderecos, {
//         id: 'enderecos',
//         cell: info => {
//             console.log(info.getValue())
//             return info.getValue().map(e => e.logradouro)
//         },
//     }),
//     columnHelper.accessor(row => row.outrosContatos, {
//         id: 'outrosContatos',
//         cell: (row) => {
//             return h('div', { class: 'font-medium' }, row.getValue())
//         },
//     }),
//     columnHelper.accessor('email', {
//         header: () => 'email',
//         footer: props => props.column.id,
//     }),
//     columnHelper.accessor('rating', {
//         header: () => 'rating',
//         footer: props => props.column.id,
//     }),
//     columnHelper.accessor('ultimoLogin', {
//         header: () => 'ultimoLogin',
//         footer: props => props.column.id,
//     }),
//     columnHelper.display({
//         id: 'actions',
//         cell: ({ row }) => {
//             const payment = row.original

//             return h('div', { class: 'relative' }, h(DataTableDropDown, {
//                 payment,
//             }))
//         },
//     }),
// ]
