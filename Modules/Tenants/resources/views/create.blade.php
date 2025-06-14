<form action="{{ route('tenants.store') }}" method="POST">
    @csrf
    <label>Tenant Name:</label>
    <input type="text" name="name" required>

    <label>Tenant ID (unique):</label>
    <input type="text" name="tenant_id" required>

    <label>Domain:</label>
    <input type="text" name="domain" required>

    <button type="submit">Create Tenant</button>
</form>
