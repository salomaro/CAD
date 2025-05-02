
        document.querySelectorAll('.workers-table button').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.cells[0].textContent;
                const name = row.cells[1].textContent;
                const schedule = row.cells[2].textContent;
                
                document.getElementById('selected-worker').textContent = `${name} (${id})`;
                document.getElementById('current-schedule').textContent = schedule;
                
                document.querySelector('.assign-form').scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.addEventListener('click', function() {
                document.querySelectorAll('.time-slot').forEach(s => {
                    s.classList.remove('selected');
                });
                this.classList.add('selected');
            });
        });