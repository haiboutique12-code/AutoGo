document.addEventListener('DOMContentLoaded',()=>{
    const start=document.querySelector('[name="start_date"]');
    const end=document.querySelector('[name="end_date"]');
    if(start&&end){
        const form=start.closest('.booking-form');
        const total=document.querySelector('#booking-total');
        const rent=document.querySelector('#booking-rent');
        const insuranceCost=document.querySelector('#booking-insurance-cost');
        const duration=document.querySelector('#booking-duration');
        const pricePerDay=Number(form?.dataset.dailyPrice||0);
        const insurancePerDay=Number(form?.dataset.insurancePrice||0);
        const deposit=Number(form?.dataset.depositAmount||0);
        const insurance=document.querySelector('#booking-insurance');
        const formatMoney=amount=>new Intl.NumberFormat('vi-VN').format(amount)+' đ';
        const updateEstimate=()=>{
            if(start.value){
                end.min=start.value;
                if(end.value&&end.value<start.value)end.value=start.value;
            }
            if(!total)return;
            if(!start.value||!end.value||end.value<start.value){
                total.textContent='Chọn ngày thuê';
                if(rent)rent.textContent='Chọn ngày thuê';
                if(insuranceCost)insuranceCost.textContent='0 đ';
                return;
            }
            const startDate=new Date(`${start.value}T00:00:00Z`);
            const endDate=new Date(`${end.value}T00:00:00Z`);
            const days=Math.floor((endDate-startDate)/86400000)+1;
            if(days<1)return;
            const rentTotal=days*pricePerDay;
            const insuranceTotal=insurance?.checked?days*insurancePerDay:0;
            if(rent)rent.textContent=formatMoney(rentTotal);
            if(insuranceCost)insuranceCost.textContent=formatMoney(insuranceTotal);
            total.textContent=formatMoney(rentTotal+insuranceTotal+deposit);
            if(duration)duration.textContent=`${days} ngày × ${formatMoney(pricePerDay)}/ngày. Tiền cọc sẽ được hoàn sau khi hoàn tất chuyến đi và kiểm tra xe.`;
        };
        start.addEventListener('change',updateEstimate);
        end.addEventListener('change',updateEstimate);
        insurance?.addEventListener('change',updateEstimate);
    }

    document.querySelectorAll('[data-toggle-password]').forEach(button=>{
        const input=document.getElementById(button.dataset.togglePassword);
        if(!input)return;
        button.addEventListener('click',()=>{
            const reveal=input.type==='password';
            input.type=reveal?'text':'password';
            button.textContent=reveal?'Ẩn':'Hiện';
            button.setAttribute('aria-label',reveal?'Ẩn mật khẩu':'Hiện mật khẩu');
        });
    });

    const toggle=document.querySelector('.menu-toggle');
    const nav=document.querySelector('#main-nav');
    if(toggle&&nav){
        const closeMenu=()=>{
            toggle.setAttribute('aria-expanded','false');
            toggle.setAttribute('aria-label','Mở menu');
            nav.classList.remove('is-open');
        };
        toggle.addEventListener('click',()=>{
            const isOpen=toggle.getAttribute('aria-expanded')==='true';
            toggle.setAttribute('aria-expanded',String(!isOpen));
            toggle.setAttribute('aria-label',isOpen?'Mở menu':'Đóng menu');
            nav.classList.toggle('is-open',!isOpen);
        });
        nav.addEventListener('click',event=>{
            if(event.target.closest('a'))closeMenu();
        });
        document.addEventListener('keydown',event=>{
            if(event.key==='Escape')closeMenu();
        });
        window.addEventListener('resize',()=>{
            if(window.innerWidth>800)closeMenu();
        });
    }

    const filterList=document.querySelector('[data-filter-list]');
    if(filterList){
        const rows=Array.from(filterList.querySelectorAll('[data-filter-row]'));
        const search=document.querySelector('[data-filter-search]');
        const status=document.querySelector('[data-filter-status]');
        const empty=document.querySelector('[data-filter-empty]');
        const applyFilters=()=>{
            const query=(search?.value||'').trim().toLocaleLowerCase('vi');
            const selectedStatus=status?.value||'';
            let visibleCount=0;
            rows.forEach(row=>{
                const text=(row.dataset.search||row.textContent).toLocaleLowerCase('vi');
                const matchesQuery=!query||text.includes(query);
                const matchesStatus=!selectedStatus||row.dataset.status===selectedStatus;
                row.hidden=!(matchesQuery&&matchesStatus);
                if(!row.hidden)visibleCount++;
            });
            if(empty&&rows.length)empty.hidden=visibleCount!==0;
        };
        search?.addEventListener('input',applyFilters);
        status?.addEventListener('change',applyFilters);
    }

    const carForm=document.querySelector('.admin-form');
    if(carForm){
        const heading=carForm.querySelector('.admin-form-heading h2');
        const defaultHeading=heading?.textContent||'Thêm hoặc cập nhật xe';
        carForm.addEventListener('reset',()=>window.setTimeout(()=>{
            const id=carForm.querySelector('[name=id]');
            if(id)id.value='';
            if(heading)heading.textContent=defaultHeading;
        }));
    }
});

function editCar(c){
    const f=document.querySelector('.admin-form');
    if(!f)return;
    f.querySelector('[name=id]').value=c.id;
    ['name','brand','type','seats','price','image','description'].forEach(k=>{
        const el=f.querySelector(`[name="${k}"]`);
        if(el)el.value=k==='price'?c.price_per_day:c[k]??'';
    });
    f.querySelector('[name=active]').checked=Number(c.active)===1;
    const heading=f.querySelector('.admin-form-heading h2');
    if(heading)heading.textContent=`Cập nhật ${c.name}`;
    f.scrollIntoView({behavior:'smooth'});
}
