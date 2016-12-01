#!/bin/bash 

# Script parameters
#	Param 1: Script run location
#	Param 2: global profile name
#	Param 3: local profile name
#	Param 4-: Any passed argument
#
# When calling oi use the --profile=PROFILE_NAME and 
# --global-profile=PROFILE_NAME argument to ensure calling scripts
# with the right profile.
#
# To post back oi commands print command prefixed by command| to standard output
# To post a comment print to std output prefixed by comment|
# To post an error print to std output prefixed by error|

function forHost {
    PORT=""
    PARAMETERS=""
    FORWARDPREFIX=""
    FORWARDS=""
    FORWARDSPRINT=""
    HOST="ack@home.acken.no"
    if [ "$1" == "silencio" ]; then
        PORT="110"
    fi
    if [ "$1" == "home" ]; then
        PORT="22"
    fi
    if [ "$1" == "repositories" ]; then
        PORT="113"
    fi
    if [ "$1" == "trueafrican" ]; then
        PORT="115"
        HOST="ack@ta.acken.no"
        PARAMETERS="-X"
        FORWARDS="-L 58080:localhost:8080"
        FORWARDSPRINT="8080 - 58080 -> Upsource server"
        FORWARDPREFIX="5"
    fi
    if [ "$1" == "trueafrican-sharedspace" ]; then
        PORT="115"
        HOST="sharedspace@ta.acken.no"
    fi
    if [ "$1" == "devbox" ]; then
        PORT="112"
        PARAMETERS="-X"
        FORWARDPREFIX="2"
    fi
    if [ "$1" == "melindev" ]; then
        PORT="114"
        PARAMETERS="-X"
        FORWARDS="-L 1080:localhost:1080 -L 1081:localhost:1081 -L 1082:localhost:1082 -L 1083:localhost:1083 -L 1084:localhost:1084 -L 1085:localhost:1085 -L 45673:localhost:5672 -L 45672:localhost:15672 -L 49200:localhost:9200 -L 43306:localhost:3306 -L 44000:localhost:44000 -L 44001:localhost:44001  -L 44002:localhost:44002 -L 44003:localhost:44003 -L 47017:localhost:27017 -L 48083:localhost:8083 -L 48084:localhost:8084 -L 48085:localhost:8085 -L 48086:localhost:8086 -L 48087:localhost:8087 -L 48088:localhost:8088 -L 48089:localhost:8089 -L 48090:localhost:8090"
        FORWARDSPRINT="1080:1085 - 1080:1085 -> Melin portal\n45627,45673 - 15672,5672 -> RabbitMQ\n49200 - 9200 -> Elastic search\n43306 - 3306 -> MySQL\n44000:44003 - 44000:44003 -> melin-portal-source CT\n47017:27917 -> Mongodb\n48083-48090 - Worker stats"
        FORWARDPREFIX="4"
    fi
}

if [ "$2" = "get-command-definitions" ]; then
	# Definition format usually represented as a single line:
    declare -a HOSTS=("silencio" "home" "devbox" "repositories" "trueafrican" "trueafrican-sharedspace" "melindev");

	echo "Handle ssh connections|"
    echo "connect|\"Connect to ssh server\" "
    for host in "${HOSTS[@]}"; do
        forHost $host
        if [ "$PORT" != "" ]; then
            echo "  ${host}|\"\" end "
        fi
    done
    echo "end "
    echo "forward|\"Setup port forwards for server\" "
    for host in "${HOSTS[@]}"; do
        forHost $host
        if [ "$FORWARDPREFIX" != "" ]; then
            echo "  ${host}|\"\" end "
        fi
    done
    echo "end "
    echo "unforward|\"Setup port forwards for server\" "
    for host in "${HOSTS[@]}"; do
        forHost $host
        if [ "$FORWARDPREFIX" != "" ]; then
            echo "  ${host}|\"\" end "
        fi
    done
    echo "end "
	exit
fi

forHost $5

if [ "$4" == "connect" ]; then
    if [ "$PORT" != "" ]; then
        gnome-terminal --window --maximize -x ssh -p $PORT $PARAMETERS $HOST
    fi
fi

if [ "$4" == "forward" ]; then
    if [ "$FORWARDPREFIX" != "" ]; then
        BASEFORWARDS="-L ${FORWARDPREFIX}3000:127.0.0.1:3000 -L ${FORWARDPREFIX}3001:127.0.0.1:3001 -L ${FORWARDPREFIX}3002:127.0.0.1:3002 -L ${FORWARDPREFIX}3003:127.0.0.1:3003"
        echo "${FORWARDPREFIX}3000:${FORWARDPREFIX}3003 - 3000:3003 -> Generic ports"
        if [ "$FORWARDSPRINT" != "" ]; then
            echo -e $FORWARDSPRINT
        fi
        ssh -fN -p $PORT $BASEFORWARDS $FORWARDS $HOST &
    fi
fi

if [ "$4" == "unforward" ]; then
    if [ "$FORWARDPREFIX" != "" ]; then
        ps -Af|grep "p $PORT -L ${FORWARDPREFIX}3000"|awk '{print $2}'|xargs kill
    fi
fi

