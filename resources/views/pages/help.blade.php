<x-guest-layout :wide="true">
    <h2 class="text-base font-bold text-gray-900 mb-4">Help &amp; User Guide</h2>

    <div class="space-y-5 text-sm text-gray-600 leading-relaxed">

        <div>
            <h3 class="font-semibold text-gray-800 mb-1">Getting Started</h3>
            <p>Log in with your credentials provided by your administrator. Your role determines which sections of the application are available to you.</p>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 mb-1">Roles</h3>
            <ul class="list-disc list-inside space-y-1">
                <li><strong>Admin</strong> — Full access: manage users, transactions, projects and rates.</li>
                <li><strong>Cost Manager</strong> — Create estimates and view project forecasts.</li>
                <li><strong>Reviewer</strong> — View analytics and rate library only.</li>
            </ul>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 mb-1">Creating an Estimate</h3>
            <p>Navigate to <em>Create Estimate</em> in the sidebar. Select a region and enter the gross floor area. Choose a cost band (Low / Medium / High / High+) for each construction element, then apply any add-ons (Externals, Project Support, Contingency, DD Risk) as required. The total updates in real time.</p>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 mb-1">Cost Bands</h3>
            <ul class="list-disc list-inside space-y-1">
                <li><strong>Low</strong> — Minimum observed rate across historical projects in the region.</li>
                <li><strong>Medium</strong> — Average rate.</li>
                <li><strong>High</strong> — Maximum observed rate.</li>
                <li><strong>High+</strong> — High rate plus a 15–20% uplift for contingency planning.</li>
            </ul>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 mb-1">Importing Transactions</h3>
            <p>Admins can upload historical cost data via <em>Transactions → Import CSV</em>. The file must be a CSV with columns: Date, PD Code, Area Code, AC Code MH, Project Nr, Item Description, Amount. After importing, run <em>Rates → Recalculate</em> to update the regional rate library.</p>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 mb-1">Support</h3>
            <p>For technical issues or account requests, contact your system administrator.</p>
        </div>

    </div>
</x-guest-layout>
