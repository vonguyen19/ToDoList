<div class="add">
    <form action="?controller=work&action=store" method="post">
        <div>
            <label for="workName">Work name:</label>
            <input type="text" id="workName" name="workName" required>
        </div>
        <div>
            <label for="startDate">Starting Date:</label>
            <input type="date" id="startDate" name="startDate" required>
        </div>
        <div>
            <label for="endDate">Ending Date:</label>
            <input type="date" id="endDate" name="endDate" required>
        </div>
        <div>
        <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="Planning">Planning</option>
                <option value="Doing">Doing</option>
                <option value="Complete">Complete</option>
            </select>
        </div>

        <button type="submit">Add</button>

    </form>

</div>