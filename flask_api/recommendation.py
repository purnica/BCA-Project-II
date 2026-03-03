import pymysql
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


def get_db_connection():
    return pymysql.connect(
        host="localhost",
        user="root",
        password="Mysql@123",   # change if needed
        database="project6",
        cursorclass=pymysql.cursors.DictCursor
    )

def fetch_courses():
    conn = get_db_connection()
    cursor = conn.cursor()

    query = """
        SELECT id, category, title, primary_description, learning_outcomes
        FROM course
    """
    cursor.execute(query)
    data = cursor.fetchall()

    cursor.close()
    conn.close()

    return pd.DataFrame(data)

def fetch_learner_interests(learner_id):
    conn = get_db_connection()
    cursor = conn.cursor()

    query = """
        SELECT interests
        FROM learnerinterests
        WHERE learner_id = %s
    """
    cursor.execute(query, (learner_id,))
    data = cursor.fetchall()

    cursor.close()
    conn.close()

    interests = " ".join([row["interests"] for row in data])
    return interests

def recommend_courses(learner_id, top_n=5):

    courses_df = fetch_courses()

    if courses_df.empty:
        return []

    learner_interest_text = fetch_learner_interests(learner_id)

    if not learner_interest_text:
        return []

    # Combine course text features
    courses_df["combined_features"] = (
        courses_df["category"].fillna("") + " " +
        courses_df["title"].fillna("") + " " +
        courses_df["primary_description"].fillna("") + " " +
        courses_df["learning_outcomes"].fillna("")
    )

    # Create TF-IDF Vectorizer
    vectorizer = TfidfVectorizer(stop_words="english")

    tfidf_matrix = vectorizer.fit_transform(courses_df["combined_features"])

    learner_vector = vectorizer.transform([learner_interest_text])

    similarity_scores = cosine_similarity(learner_vector, tfidf_matrix)

    scores = similarity_scores.flatten()

    courses_df["similarity_score"] = scores

    # Remove zero similarity results
    courses_df = courses_df[courses_df["similarity_score"] > 0]

    if courses_df.empty:
        return []

    recommended = courses_df.sort_values(
        by="similarity_score",
        ascending=False
    ).head(top_n)

    return recommended[["id", "title", "category", "similarity_score"]].to_dict(orient="records")
