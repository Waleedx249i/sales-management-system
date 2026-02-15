<script>
    function openSaleModal(itemId, productName, maxQty) {
        if(maxQty <= 0) {
            alert('عفواً، الكمية نفدت تماماً من المندوب');
            return;
        }
        
        const modal = document.getElementById('saleModal');
        const form = document.getElementById('saleForm');
        const productNameLabel = document.getElementById('modalProductName');
        const qtyInput = document.getElementById('maxQtyInput');

        modal.classList.remove('hidden');
        productNameLabel.innerText = `${productName} (المتبقي: ${maxQty})`;
        
        // استخدام الرابط الصحيح
        form.action = `/pos-consignments/record-sale/${itemId}`;
        
        qtyInput.max = maxQty;
        qtyInput.value = 1;
    }

    function closeModal() {
        document.getElementById('saleModal').classList.add('hidden');
    }

    // إغلاق عند الضغط خارج المودال
    window.onclick = function(event) {
        let modal = document.getElementById('saleModal');
        if (event.target == modal) closeModal();
    }
</script>