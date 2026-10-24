# Running Bloodhound Revival on ChromeOS

## Recommended setup

Use the Chromebook's Linux development environment (often called Crostini) and install Docker Engine inside its Debian Linux environment, if supported by your Chromebook and organization policy. ChromeOS does not support Docker Desktop as a native ChromeOS application.

1. Open **Settings → About ChromeOS → Developers** and enable the Linux development environment if the option is available.
2. Open the **Terminal** app and update packages:

   ```sh
   sudo apt update
   sudo apt upgrade
   ```

3. Install Docker Engine using Docker's official Debian instructions for the Debian version provided by ChromeOS. Use Docker's official repository rather than an old distribution package when possible:
   https://docs.docker.com/engine/install/debian/
4. Follow Docker's post-install instructions. For the first test, run Docker with `sudo` if needed; do not change broad system permissions just to avoid `sudo`.
5. Verify:

   ```sh
   sudo docker version
   sudo docker run --rm hello-world
   sudo docker compose version
   ```

6. Clone the Bloodhound repository into the Linux files area, switch to `revival/docker-bootstrap`, and follow [LOCAL_DOCKER.md](LOCAL_DOCKER.md).

Exact steps depend on ChromeOS version, device model, Linux container version, and whether the Chromebook is managed by an organization. If Linux development environment is unavailable or Docker cannot start containers, use a supported Linux VM or remote Linux host for the compatibility workload.

## Resource guidance

- Keep the first test small; do not run packet capture on the Chromebook's network interface.
- Store the repository and test files in the Linux files area, not a shared Downloads folder.
- Do not mount the Chromebook's home directory or broad host paths into the legacy processor container.
- The initial database is bound to `127.0.0.1` only.
- Container nesting, kernel features, memory, and disk capacity may limit performance in Crostini.

## Safe test traffic

Use [generate_sample_pcap.py](../tests/fixtures/generate_sample_pcap.py) to generate invented traffic only. It uses documentation-only IP ranges and the reserved `.test` domain. Do not substitute live packet captures until the processing path is validated and data-handling requirements are understood.
