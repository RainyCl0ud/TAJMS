<div id="clock" class="bg-white rounded-lg shadow-md h-16 pl-14 pb-5">
    <h2 class="font-bold" id="time"></h2>    
</div>
<script>
    function updateClock() {
    const now = new Date();
    let hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12; // Convert to 12-hour format
    hours = hours ? hours : 12; // Hour '0' should be '12'
    const timeString = `${hours}:${minutes} ${ampm}`;
    const timeElement = document.getElementById('time');

    if (timeElement) {
        timeElement.innerText = timeString; // Display the time
    }
}

setInterval(updateClock, 1000); // Update the clock every second
updateClock(); // Call it once initially

</script>