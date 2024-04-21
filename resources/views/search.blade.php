<div class="container main-container mt-3"> <!-- Use container-fluid to make it full width -->
    <form action="{{ route('search') }}" method="GET" class="search-form">
        <div class="input-group"> <!-- Use input-group to group input and button -->
            <input type="text" name="query" class="form-control" placeholder="Search...(IMDB ID or Title(Any Language)...)">
            <button type="submit" class="btn btn-success">Search</button>
        </div>
    </form>

<style>
.main-container {
    background-color: #1D3138; /* Background color */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Box shadow */
    padding: 20px; /* Add padding for better appearance */
    border-radius: 10px; /* Optional: Add border radius for rounded corners */
}


</style>
</div>
