# Bloodhound Phase 1: BHMM transport and ingestion toolchain.
# This image supplies the dependencies; orchestration scripts are mounted
# separately so they can be reviewed and changed without rebuilding the image.
FROM debian:bookworm-slim

ENV DEBIAN_FRONTEND=noninteractive \
    PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1

RUN apt-get update && apt-get install -y --no-install-recommends \
      argus-client \
      argus-server \
      ca-certificates \
      coreutils \
      file \
      findutils \
      gzip \
      openssh-client \
      python3 \
      python3-pymysql \
      tcpdump \
    && rm -rf /var/lib/apt/lists/* \
    && groupadd --gid 10001 bloodhound \
    && useradd --uid 10001 --gid 10001 --create-home --shell /bin/bash bloodhound \
    && install -d -o bloodhound -g bloodhound \
         /data/inbound /data/processing /data/processed /data/archive \
         /home/bloodhound/.ssh

ENV HOME=/home/bloodhound
WORKDIR /opt/bloodhound

USER bloodhound

# Keep the container idle until an explicit, reviewed transfer/processing
# command is run. Starting the container alone must not pull or delete files.
CMD ["sleep", "infinity"]
