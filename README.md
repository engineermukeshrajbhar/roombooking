# Room Booking System

A PHP-based room booking application with queue management, automatic expiry, and admin dashboard.

## Features

- 🏨 **Room Management**: 3 bookable rooms with 2 concurrent bookings each
- ⏳ **Queue System**: Automatic queue management when rooms are full
- ⏱️ **Time-Limited Bookings**: 30-minute booking window (configurable)
- 👨‍💻 **Admin Dashboard**: View and manage all bookings
- 📅 **Automatic Processing**: Expired bookings are automatically cancelled

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (recommended for dependencies)

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/room-booking-system.git
   cd room-booking-system
