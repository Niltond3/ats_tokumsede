/**
 * Creates a DataTable filter function to control product status visibility
 * @param {boolean} showActiveOnly - Flag to show only active products
 * @returns {Function} DataTable filter function
 *
 * @example
 * // Basic usage
 * const filterFn = createStatusFilter(showActiveOnly)
 * $.fn.dataTable.ext.search.push(filterFn)
 *
 * // With watch
 * watch(showActiveOnly, () => {
 *   dt.draw()
 * })
 */
export const createStatusFilter = (showActiveOnly) => {
    return (settings, data, dataIndex) => {
        // Early return if filter is off
        if (!showActiveOnly) return true;

        // Get full row data from DataTable API
        const api = new $.fn.dataTable.Api(settings);
        const rowData = api.row(dataIndex).data();
        if (rowData.produto === undefined) return true;

        // Check product status (1 = active)
        return rowData.produto.status === 1;
    };
};
