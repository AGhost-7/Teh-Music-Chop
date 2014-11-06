name := "Project Parsers"

version := "1.0"

resolvers ++= Seq(
	"Typesafe Releases" at "http://repo.typesafe.com/typesafe/releases/"
)
  
libraryDependencies ++= Seq(
	"org.ccil.cowan.tagsoup" % "tagsoup" % "1.2",
	"mysql" % "mysql-connector-java" % "5.1.6",
	"com.typesafe.play" %% "anorm" % "2.3.2"
)
