# 使ったことあるもの
show tables, databases
create database, create
ALTER TABLE
desc, 
select, update, insert, delete, truncate, insert ~ select
CASE
where, IN OR AND
order
group by
INNER JOIN, LEFT OUTER JOIN,
CONCAT, REPLACE,
サブクエリ
ストアド

# 知っているが使ったことないもの
having, partition, with, explain, trigger, lead

# 使ったことあるtool
PHPMyAdmin, MySQL Workbench, DBeaver


# その他
DB	CRUD	100本ノック			
		副問合せ	基本的に遅いので単発で使用するがベスト		
		WITH			
	クエリチューニング		"1,スローログの設定（何秒以上かかっているものをログに出させる）
2,実際のクエリが出るのでExplainする。
3,インデックスが使われているか、全件スキャンしていないか、件数が多すぎないかをチェック
4,諸々注意してチューニング。
※なお、UNIONやJOINよりもCASEの方が早い場合がある。副問合せは原則遅い"		
	DB設計				
	インデックス		インデックスが使われない条件、関数、否定など		