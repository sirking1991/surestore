# SureStore

<p align="center">
<a href="https://github.com/sirking1991/surestore/actions"><img src="https://github.com/sirking1991/surestore/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/sirking1991/surestore"><img src="https://img.shields.io/packagist/v/sirking1991/surestore" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/sirking1991/surestore"><img src="https://img.shields.io/packagist/l/sirking1991/surestore" alt="License"></a>
</p>

## About SureStore

SureStore is a comprehensive Manufacturing and Inventory Management System built with Laravel. It provides end-to-end solutions for manufacturing businesses, from raw material procurement to production management and sales.

## Key Features

### Production Management
- Track production processes with start/end times and costs
- Record labor costs, setup time, and production time
- Calculate efficiency ratios (output value vs. input cost)
- Manage raw materials used and products produced

### Work Order System
- Create and track work orders with priority levels (low, medium, high, urgent)
- Schedule production tasks with estimated completion times
- Assign work to specific users
- Break down work orders into specific tasks (work order items)
- Track completion percentage of work orders and individual tasks

### Inventory Management
- Distinguish between raw materials and finished products
- Track products that can be produced in-house
- Manage storage locations for materials and products
- Record inventory adjustments

### Sales Pipeline
- Handle quotes, orders, deliveries, invoices, and payments
- Track customer information and relationships

### Purchasing System
- Manage purchase orders, deliveries, invoices, and disbursements
- Track supplier information and relationships

## System Architecture

SureStore uses a relational database with well-defined relationships between:
- Productions and their materials/products
- Work orders and their items
- Products and their storage locations
- Users and their assigned tasks

## Notable Functionality

- **Cost Tracking**: Detailed tracking of material costs, labor costs, and production efficiency
- **Time Management**: Scheduling, time tracking, and deadline management
- **Task Assignment**: Work can be assigned to specific users
- **Status Tracking**: All entities have clear status progressions
- **Efficiency Calculations**: The system calculates efficiency ratios and completion percentages

## Getting Started

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL or compatible database
- Node.js and NPM (for frontend assets)

### Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/surestore.git
cd surestore
```

2. Install dependencies
```bash
composer install
npm install
```

3. Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in the `.env` file

5. Run migrations and seed the database
```bash
php artisan migrate
php artisan db:seed
```

6. Compile assets
```bash
npm run dev
```

7. Start the development server
```bash
php artisan serve
```

## License

The SureStore application is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
