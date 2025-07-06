/*document.addEventListener("DOMContentLoaded", function () {
    const popup = document.getElementById("popup");
    const closeBtn = document.getElementById("closePopup");
    const saveBtn = document.getElementById("saveReportBtn");

    let currentStudentId = null;

    // فتح النافذة عند الضغط على زر 📋
    document.querySelectorAll(".open-popup-btn").forEach(button => {
        button.addEventListener("click", function () {
            currentStudentId = this.getAttribute("data-student-id");
            document.getElementById("fromAyah").value = '';
            document.getElementById("toAyah").value = '';
            document.getElementById("surahSelect").innerHTML = '<option disabled selected>Loading...</option>';

            // تحميل السور من قاعدة البيانات
            fetch('get_surahs.php')
                .then(response => response.json())
                .then(data => {
                    const surahSelect = document.getElementById("surahSelect");
                    surahSelect.innerHTML = '';
                    data.forEach(surah => {
                        const option = document.createElement("option");
                        option.value = surah.id;
                        option.textContent = surah.name;
                        option.setAttribute("data-verse-count", surah.verses_count);
                        surahSelect.appendChild(option);
                    });
                });

            popup.style.display = "block";
        });
    });

    closeBtn.addEventListener("click", () => {
        popup.style.display = "none";
    });

    saveBtn.addEventListener("click", () => {
        const surahId = document.getElementById("surahSelect").value;
        const fromAyah = parseInt(document.getElementById("fromAyah").value);
        const toAyah = parseInt(document.getElementById("toAyah").value);

        if (!surahId || isNaN(fromAyah) || isNaN(toAyah) || fromAyah > toAyah) {
            alert("Please enter valid ayah range.");
            return;
        }

        fetch('save_report.php', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                student_id: currentStudentId,
                surah_id: surahId,
                from_ayah: fromAyah,
                to_ayah: toAyah
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // تحديث الواجهة مباشرة
                document.querySelector(`#lastSurah-${currentStudentId}`).textContent = data.last_memorized;
                document.querySelector(`#progress-${currentStudentId} .bar`).style.width = data.progress + "%";
                document.querySelector(`#progress-${currentStudentId} .progress-text`).textContent = data.progress + "%";
                popup.style.display = "none";
            } else {
                alert("Error saving report.");
            }
        })
        .catch(error => {
            console.error(error);
            alert("Something went wrong.");
        });
    });
});*/
