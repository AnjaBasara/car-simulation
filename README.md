# Car Simulator technical exercise

### Running the Dockerized application

To run this application, you should have [Docker](https://www.docker.com/) installed.

---

After cloning the repository, open the `car-simulator` folder and run this command:

```bash
docker-compose up -d
```

After that, to run the **ProcessCarEvents** artisan command, execute the Docker container interactively by running the command:

`docker exec -it car-simulator-app php artisan app:process-car-events <path_to_csv>`

where `<path_to_csv>` is the path where your CSV files are located withing the Laravel project, e.g.:

```bash
docker exec -it car-simulator-app php artisan app:process-car-events storage/app/230913_003.csv
```

---

### Running tests

To run tests, execute the Docker container interactively by running the command:

```bash
docker exec -it car-simulator-app php artisan test
```

---

Thank you for your time!
