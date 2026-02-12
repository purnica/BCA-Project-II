from flask import Flask, jsonify, request
from flask_cors import CORS
from recommendation import recommend_courses

app = Flask(__name__)
CORS(app)


@app.route("/recommend/<int:learner_id>", methods=["GET"])
def recommend(learner_id):
    try:
        top_n = request.args.get("limit", default=5, type=int)

        recommendations = recommend_courses(learner_id, top_n)

        return jsonify({
            "status": "success",
            "recommendations": recommendations
        })

    except Exception as e:
        return jsonify({
            "status": "error",
            "message": str(e)
        }), 500


if __name__ == "__main__":
    app.run(debug=True, port=5000)
