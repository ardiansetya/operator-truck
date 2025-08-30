````markdown
# Operator Truck
## 🚀 Cara Menjalankan Project
````

1. Masuk ke folder project

   ```bash
   git clone https://github.com/ardiansetya/operator-truck.git
   ```

2. Masuk ke folder project

   ```bash
   cd operator-truck
   ```

3. Salin file environment

   ```bash
   cp .env.example .env
   ```

4. Atur permission untuk folder `storage` dan `bootstrap/cache`

   ```bash
   sudo chown -R 1000:1000 storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

5. Build dan jalankan container dengan Docker Compose

   ```bash
   docker-compose up -d --build
   ```

✅ Setelah itu aplikasi sudah jalan di container.
