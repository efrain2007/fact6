
export const inventory_search_item_barcode = {
    data() {
        return {
            search_item_by_barcode: false,
        }
    },
    methods: 
    {
        async enabledSearchItemsBarcode() 
        {
            if (this.search_item_by_barcode) 
            {
                this.$refs.select_barcode.$data.selectedLabel = ''

                if (this.items.length == 1) 
                {
                    this.form.item_id = this.items[0].id
                    this.$refs.select_barcode.blur()
                    await this.changeItem()
                    this.form.quantity = parseInt(this.form.quantity) + 1
                    this.$refs.select_barcode.$el.getElementsByTagName('input')[0].focus()
                }
            }
        },
    }
}