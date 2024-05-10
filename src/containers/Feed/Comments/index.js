import React from 'react';
import { FlatList, StyleSheet } from 'react-native';
import { Container, NavBar, Empty } from 'components';
import { getNComments } from 'mocks/comments';
import PropTypes from 'prop-types';
import { scale } from 'react-native-size-matters';
import CommentItem from './CommentItem';

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: scale(14),
    paddingTop: scale(14),
    flexGrow: 1,
  },
});

const Comments = ({ navigation, route }) => {
  const { numberOfComments } = route.params;
  const comments = getNComments(numberOfComments);
  return (
    <Container>
      <NavBar title="Comments" onLeftIconPress={() => navigation.goBack()} />
      <FlatList
        contentContainerStyle={styles.container}
        data={comments}
        keyExtractor={(data) => data?.id?.toString()}
        renderItem={({ item }) => (
          <CommentItem {...item} />
        )}
        ListEmptyComponent={Empty}
      />
    </Container>
  );
};

Comments.propTypes = {
  navigation: PropTypes.object.isRequired,
  route: PropTypes.object.isRequired,
};

export default Comments;
